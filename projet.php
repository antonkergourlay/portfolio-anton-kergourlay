<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
header("Content-type: text/html; charset=utf-8");

// Adresse absolue du site, calculée automatiquement (utile pour les images d'aperçu des réseaux sociaux).
$schema = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
$baseUrl = $schema . "://" . (isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "localhost") . rtrim(dirname($_SERVER["SCRIPT_NAME"]), "/\\") . "/";

if (isset($_GET["id"])) {
    if ($_GET["id"] != "") {
        $idProjet = trim($_GET["id"]);
        $idProjet = mb_strtolower($idProjet);
        // Seuls les identifiants simples (lettres, chiffres, tirets) sont acceptés : pas de chemin vers un autre dossier.
        if (!preg_match('/^[a-z0-9-]+$/', $idProjet)) {
            header("Location: index.php#galerie");
            exit();
        }
    } else {
        header("Location: index.php#galerie");
        exit();
    }
} else {
    header("Location: index.php#galerie");
    exit();
}

$fichierProjet = "./data/projets/" . $idProjet . ".csv";

if (file_exists($fichierProjet) == false) {
    header("Location: index.php#galerie");
    exit();
}

$titre       = "";
$categorie   = "";
$annee       = "";
$contexte    = "";
$competences = "";
$tags        = "";

$mediasType  = array();
$mediasSrc   = array();
$mediasAlt   = array();
$mediasOpt   = array();
$paragraphes = array();

$lecteur = new SplFileObject($fichierProjet, "r");

while ($lecteur->eof() == false) {
    $ligne = $lecteur->fgets();
    $ligne = trim($ligne);

    if ($ligne != "") {
        $tab = explode(";", $ligne);

        if (count($tab) >= 2) {
            $cle = trim($tab[0]);

            if ($cle == "titre") {
                $titre = trim($tab[1]);
            } else if ($cle == "categorie") {
                $categorie = trim($tab[1]);
            } else if ($cle == "annee") {
                $annee = trim($tab[1]);
            } else if ($cle == "contexte") {
                $contexte = trim($tab[1]);
            } else if ($cle == "competences") {
                $competences = trim($tab[1]);
            } else if ($cle == "tags") {
                $tags = trim($tab[1]);
            } else if ($cle == "media") {
                if (count($tab) >= 4) {
                    $mediasType[] = trim($tab[1]);
                    $mediasSrc[]  = trim($tab[2]);
                    $mediasAlt[]  = trim($tab[3]);
                    $mediasOpt[]  = count($tab) >= 5 ? trim($tab[4]) : "";
                }
            } else if ($cle == "section") {
                $mediasType[] = "section";
                $mediasSrc[]  = trim($tab[1]);
                $mediasAlt[]  = count($tab) >= 3 ? trim($tab[2]) : "";
                $mediasOpt[]  = "";
            } else if ($cle == "texte") {
                $p = "";
                $i = 1;
                while ($i < count($tab)) {
                    $p = $p == "" ? trim($tab[$i]) : $p . ";" . trim($tab[$i]);
                    $i++;
                }
                $p = trim($p);
                if ($p != "") {
                    $paragraphes[] = $p;
                }
            }
        }
    }
}

$lecteur = null;

if ($titre == "") {
    header("Location: index.php#galerie");
    exit();
}

$ordreProjets = array(
    array("id" => "zest", "titre" => "ZEST", "sous_titre" => "Packaging · 2026"),
    array("id" => "tour-90-couleurs", "titre" => "Tour du monde en 90 couleurs", "sous_titre" => "Peinture · Dessin · 2024–2025"),
    array("id" => "magazine-urasawa", "titre" => "Magazine – Naoki Urasawa", "sous_titre" => "Édition · 2026"),
    array("id" => "flyer-trail-croix", "titre" => "Flyer – Trail de la Croix", "sous_titre" => "Affiche · 2025"),
    array("id" => "menace-santana", "titre" => "Univers menace Santana", "sous_titre" => "Projet personnel · 2022–2024"),
);

$visuelsProjets = array(
    "zest" => array("./images/projets/zest/cover-wide.webp", "Canettes ZEST en motif sur fond vert"),
    "tour-90-couleurs" => array("./images/projets/tour-90-couleurs/cover-wide.webp", "Quatre œuvres exposées sur des panneaux dans la rue"),
    "magazine-urasawa" => array("./images/projets/magazine-urasawa/cover-wide.webp", "Double page du magazine Naoki Urasawa"),
    "flyer-trail-croix" => array("./images/projets/flyer-trail-croix/cover-wide.webp", "Affiches du Trail de la Croix sur un mur de briques"),
    "menace-santana" => array("./images/projets/menace-santana/cover-wide.webp", "Quatre masques menace Santana sur fond noir"),
);

// Cadrage de l'image d'ouverture (x y), pour garder le sujet visible quand elle est recadrée.
$positionsOuverture = array("flyer-trail-croix" => "50% 45%", "magazine-urasawa" => "50% 50%", "menace-santana" => "50% 50%");
$positionOuverture = isset($positionsOuverture[$idProjet]) ? $positionsOuverture[$idProjet] : "50% 50%";

// Visuels d'ouverture très larges : sur mobile, ils s'affichent entiers (sans recadrage) sur fond noir.
$ouverturePanoramique = in_array($idProjet, array("menace-santana", "magazine-urasawa"));

// Proportion de l'ouverture sur mobile (par défaut 4 / 5) quand le visuel est plutôt horizontal.
$formatsMobile = array("tour-90-couleurs" => "4 / 3");
$formatMobile = isset($formatsMobile[$idProjet]) ? $formatsMobile[$idProjet] : "4 / 5";

// Rangées d'images : la première image sert d'ouverture, les suivantes sont regroupées par rangées.
// Les images sont de même hauteur dans une rangée (grâce à leur proportion) : jusqu'à 4 images verticales à la suite, sinon 2 images par rangée.
// Options en 5e colonne du CSV : "pleine" = l'image occupe seule une rangée ; "legende" = affiche la légende sous l'image ; "inverse" (sur la 1re image d'une rangée) = inverse l'ordre gauche/droite de la rangée. Une ligne "section;Titre;Description" ouvre une nouvelle partie.
// Une image très large (panoramique) ou une vidéo occupe aussi une rangée entière.
$rangees = array();
$suite = array();
for ($i = 1; $i < count($mediasType); $i++) {
    $ar = 16 / 9;
    if ($mediasType[$i] == "image") {
        $taille = @getimagesize(urldecode($mediasSrc[$i]));
        if ($taille && $taille[1] > 0) {
            $ar = $taille[0] / $taille[1];
        }
    }
    $opt = isset($mediasOpt[$i]) ? $mediasOpt[$i] : "";
    $suite[] = array(
        "type" => $mediasType[$i], "src" => $mediasSrc[$i], "alt" => $mediasAlt[$i], "ar" => $ar,
        "seule" => (strpos($opt, "pleine") !== false || $ar >= 1.7 || $mediasType[$i] != "image"),
        "legende" => (strpos($opt, "legende") !== false),
        "inverse" => (strpos($opt, "inverse") !== false)
    );
}
$k = 0;
while ($k < count($suite)) {
    $rangee = array($suite[$k]);
    if (!$suite[$k]["seule"]) {
        $vertical = ($suite[$k]["ar"] < 1);
        $max = $vertical ? 4 : 2;
        $j = $k + 1;
        while ($j < count($suite) && count($rangee) < $max && !$suite[$j]["seule"] && (!$vertical || $suite[$j]["ar"] < 1)) {
            $rangee[] = $suite[$j];
            $j++;
        }
    }
    // Option "inverse" sur la première image d'une rangée : on affiche la rangée à l'envers (image étroite à gauche).
    if ($rangee[0]["inverse"]) {
        $rangee = array_reverse($rangee);
    }
    $rangees[] = $rangee;
    $k += count($rangee);
}

// "Autres projets" : les 3 projets qui suivent celui-ci dans l'ordre de l'accueil (on reboucle à la fin), pour que chaque projet soit proposé quelque part.
$projetsAutres = array();
$positionCourante = 0;
foreach ($ordreProjets as $position => $projet) {
    if ($projet["id"] === $idProjet) {
        $positionCourante = $position;
    }
}
for ($decalage = 1; $decalage < count($ordreProjets) && count($projetsAutres) < 3; $decalage++) {
    $projet = $ordreProjets[($positionCourante + $decalage) % count($ordreProjets)];
    if (isset($visuelsProjets[$projet["id"]])) {
        $projetsAutres[] = $projet;
    }
}

if (!isset($visuelsProjets[$idProjet])) {
    header("Location: index.php#galerie");
    exit();
}


// Référencement : titre, description et adresse canonique propres à chaque projet.
$urlCanonique = "https://antonkergourlay.fr/projet.php?id=" . $idProjet;
$imageSeo = "https://antonkergourlay.fr/images/previews/preview-" . $idProjet . ".jpg";
$titreSeo = $titre . " — " . $categorie . " | Anton Kergourlay";
if (mb_strlen($titreSeo) > 62) {
    $titreSeo = $titre . " | Anton Kergourlay";
}
$descriptionSeo = $titre . " : " . $categorie . ". ";
if (count($paragraphes) > 0) {
    $extrait = trim(preg_replace('/\s+/u', ' ', strip_tags($paragraphes[0])));
    $reste = 158 - mb_strlen($descriptionSeo);
    if ($reste > 40) {
        $descriptionSeo .= mb_strlen($extrait) > $reste ? rtrim(mb_substr($extrait, 0, $reste - 1), " ,;:.") . "…" : $extrait;
    }
}
$descriptionSeo = trim($descriptionSeo) . (mb_strlen($descriptionSeo) < 80 ? " Projet du portfolio d'Anton Kergourlay." : "");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($descriptionSeo); ?>">
    <link rel="canonical" href="<?php echo htmlspecialchars($urlCanonique); ?>">
    <meta property="og:site_name" content="Anton Kergourlay">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo htmlspecialchars($urlCanonique); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($titreSeo); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($descriptionSeo); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($imageSeo); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($titreSeo); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($descriptionSeo); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($imageSeo); ?>">
    <title><?php echo htmlspecialchars($titreSeo); ?></title>
    <script type="application/ld+json"><?php echo json_encode(array(
        "@context" => "https://schema.org",
        "@graph" => array(
            array(
                "@type" => "CreativeWork",
                "name" => $titre,
                "description" => $descriptionSeo,
                "url" => $urlCanonique,
                "image" => $imageSeo,
                "inLanguage" => "fr",
                "dateCreated" => substr($annee, 0, 4),
                "genre" => $categorie,
                "author" => array("@type" => "Person", "name" => "Anton Kergourlay", "url" => "https://antonkergourlay.fr/")
            ),
            array(
                "@type" => "BreadcrumbList",
                "itemListElement" => array(
                    array("@type" => "ListItem", "position" => 1, "name" => "Accueil", "item" => "https://antonkergourlay.fr/"),
                    array("@type" => "ListItem", "position" => 2, "name" => "Projets", "item" => "https://antonkergourlay.fr/#galerie"),
                    array("@type" => "ListItem", "position" => 3, "name" => $titre, "item" => $urlCanonique)
                )
            )
        )
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
    <link rel="icon" href="images/favicon/favicon-aker.svg?v=2" type="image/svg+xml">
    <link rel="icon" href="images/favicon/favicon.ico?v=2" sizes="32x32">
    <link rel="apple-touch-icon" href="images/favicon/apple-touch-icon.png?v=2">
    <link rel="stylesheet" href="css/styles.css?v=126" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=general-sans@300,500,700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://api.fontshare.com/v2/css?f[]=general-sans@300,500,700&display=swap" rel="stylesheet"></noscript>
</head>

<body class="page-projet">

    <!-- NAV -->
    <nav>
        <ul>
            <li class="nav-logo"><a href="index.php" aria-label="A.KER, Anton Kergourlay, accueil"><span class="logo-mark">A.KER</span></a></li>
            <li><button class="nav-hamburger" id="hamburger" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="nav-drawer">
                    <span class="nav-hamburger-lines" aria-hidden="true"><span></span><span></span></span>
                </button></li>
        </ul>
    </nav>

    <!-- Menu plein écran -->
    <?php
    $menuProjets = array();
    foreach ($ordreProjets as $projet) {
        $menuProjets[] = array("id" => $projet["id"], "titre" => $projet["titre"], "sous_titre" => $projet["sous_titre"], "cover" => $visuelsProjets[$projet["id"]][0], "pos" => isset($positionsOuverture[$projet["id"]]) ? $positionsOuverture[$projet["id"]] : "50% 50%");
    }
    $menuPrefix = "index.php";
    include "menu.php";
    ?>

    <!-- CONTENU PROJET -->
    <main class="project-page project-v2 project-<?php echo $idProjet; ?>">

        <header class="pj-hero<?php echo $ouverturePanoramique ? " pj-hero--panorama" : ""; ?>" style="--pos: <?php echo htmlspecialchars($positionOuverture); ?>; --ar-mobile: <?php echo htmlspecialchars($formatMobile); ?><?php echo $idProjet === "magazine-urasawa" ? "; --pano-bg: #dcdcdc" : ""; ?>">
            <picture>
                <?php if (file_exists("./images/projets/" . $idProjet . "/cover-tall.webp")) { ?>
                    <source media="(max-width: 700px)" srcset="./images/projets/<?php echo htmlspecialchars($idProjet); ?>/cover-tall.webp">
                <?php } ?>
                <img class="pj-hero-img" src="<?php echo htmlspecialchars($mediasSrc[0]); ?>" alt="<?php echo htmlspecialchars($mediasAlt[0]); ?>" fetchpriority="high" loading="eager">
            </picture>
            <a class="pj-back" href="index.php#galerie">← Retour aux projets</a>
            <div class="pj-hero-text">
                <p class="pj-kicker"><?php echo htmlspecialchars($categorie); ?> · <?php echo htmlspecialchars($annee); ?></p>
                <h1><?php echo htmlspecialchars($titre); ?></h1>
            </div>
        </header>

        <section class="pj-intro">
            <dl class="pj-meta">
                <?php if ($contexte != "") { ?>
                    <div><dt>Contexte</dt><dd><?php echo htmlspecialchars($contexte); ?></dd></div>
                <?php } ?>
                <?php if ($competences != "") { ?>
                    <div><dt>Réalisation</dt><dd><?php echo htmlspecialchars($competences); ?></dd></div>
                <?php } ?>
            </dl>
            <?php if (count($paragraphes) > 0) { ?>
                <p class="pj-lead"><?php echo htmlspecialchars($paragraphes[0]); ?></p>
            <?php } ?>
            <?php if (count($paragraphes) > 1) { ?>
                <div class="pj-body">
                    <?php for ($i = 1; $i < count($paragraphes); $i++) { ?>
                        <p><?php echo htmlspecialchars($paragraphes[$i]); ?></p>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>

        <?php if (count($rangees) > 0) { ?>
            <section class="pj-rows" aria-label="Images du projet">
                <?php $numSection = 0; ?>
                <?php foreach ($rangees as $rangee) { ?>
                    <?php if ($rangee[0]["type"] == "section") { $numSection++; ?>
                        <div class="pj-section">
                            <span class="pj-section-num"><?php echo sprintf("%02d", $numSection); ?></span>
                            <h2><?php echo htmlspecialchars($rangee[0]["src"]); ?></h2>
                            <?php if ($rangee[0]["alt"] != "") { ?><p><?php echo htmlspecialchars($rangee[0]["alt"]); ?></p><?php } ?>
                        </div>
                        <?php continue; ?>
                    <?php } ?>
                    <div class="pj-row">
                        <?php foreach ($rangee as $m) { ?>
                            <figure class="pj-fig" style="--ar: <?php echo round($m["ar"], 4); ?>">
                                <?php if ($m["type"] == "video") { ?>
                                    <video autoplay muted loop playsinline src="<?php echo htmlspecialchars($m["src"]); ?>"></video>
                                <?php } else { ?>
                                    <img src="<?php echo htmlspecialchars($m["src"]); ?>" alt="<?php echo htmlspecialchars($m["alt"]); ?>" loading="lazy">
                                <?php } ?>
                                <?php if ($m["legende"] && $m["alt"] != "") { ?>
                                    <figcaption><?php echo htmlspecialchars($m["alt"]); ?></figcaption>
                                <?php } ?>
                            </figure>
                        <?php } ?>
                    </div>
                <?php } ?>
            </section>
        <?php } ?>


        <?php if ($idProjet === "zest") { ?>
            <section class="zest-branding" aria-label="Déclinaisons du logo ZEST">
                <h2>Le logo en trois versions</h2>
                <div class="zest-logo-strip">
                    <figure class="zest-logo-swatch zest-logo-swatch-green">
                        <img src="./images/projets/zest/logo-vert.svg" alt="Logo ZEST vert sauge">
                        <figcaption>Vert sauge</figcaption>
                    </figure>
                    <figure class="zest-logo-swatch zest-logo-swatch-black">
                        <img src="./images/projets/zest/logo-noir.svg" alt="Logo ZEST noir">
                        <figcaption>Noir</figcaption>
                    </figure>
                    <figure class="zest-logo-swatch zest-logo-swatch-white">
                        <img src="./images/projets/zest/logo-blanc-transparent.svg" alt="Logo ZEST blanc">
                        <figcaption>Réserve blanche</figcaption>
                    </figure>
                </div>
            </section>
        <?php } ?>

        <section class="next-projects" aria-label="Autres projets">
            <?php $suivant = $projetsAutres[0]; ?>
            <a class="next-main" data-project-id="<?php echo htmlspecialchars($suivant["id"]); ?>" href="projet.php?id=<?php echo urlencode($suivant["id"]); ?>" style="--pos: <?php echo htmlspecialchars(isset($positionsOuverture[$suivant["id"]]) ? $positionsOuverture[$suivant["id"]] : "50% 50%"); ?>">
                <img src="<?php echo htmlspecialchars($visuelsProjets[$suivant["id"]][0]); ?>" alt="<?php echo htmlspecialchars($visuelsProjets[$suivant["id"]][1]); ?>" loading="lazy">
                <span class="next-kicker">Projet suivant</span>
                <span class="next-info">
                    <span class="next-title"><?php echo htmlspecialchars($suivant["titre"]); ?></span>
                    <span class="next-meta"><?php echo htmlspecialchars($suivant["sous_titre"]); ?> <span class="next-arrow" aria-hidden="true">→</span></span>
                </span>
            </a>
            <div class="next-duo">
                <?php for ($i = 1; $i < count($projetsAutres); $i++) { $autre = $projetsAutres[$i]; ?>
                    <a class="next-small" data-project-id="<?php echo htmlspecialchars($autre["id"]); ?>" href="projet.php?id=<?php echo urlencode($autre["id"]); ?>" style="--pos: <?php echo htmlspecialchars(isset($positionsOuverture[$autre["id"]]) ? $positionsOuverture[$autre["id"]] : "50% 50%"); ?>">
                        <img src="<?php echo htmlspecialchars($visuelsProjets[$autre["id"]][0]); ?>" alt="<?php echo htmlspecialchars($visuelsProjets[$autre["id"]][1]); ?>" loading="lazy">
                        <span class="next-info">
                            <span class="next-title"><?php echo htmlspecialchars($autre["titre"]); ?></span>
                            <span class="next-meta"><?php echo htmlspecialchars($autre["sous_titre"]); ?> <span class="next-arrow" aria-hidden="true">→</span></span>
                        </span>
                    </a>
                <?php } ?>
            </div>
            <a class="next-all" href="index.php#galerie"><span>Tous les projets</span><span class="next-all-arrow" aria-hidden="true">→</span></a>
        </section>

    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-inner">
            <div class="footer-columns">
                <div class="footer-brand">
                    <p>Anton <em>Kergourlay</em></p>
                    <span>Étudiant en BUT MMI — IUT Laval<br>Graphisme &amp; création visuelle</span>
                </div>
                <div class="footer-col">
                    <h3>Prestations</h3>
                    <ul>
                        <li>Graphisme</li>
                        <li>Création visuelle</li>
                        <li>Édition et print</li>
                        <li>Webdesign</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Me suivre</h3>
                    <?php echo liens_reseaux("small"); ?>
                </div>
                <div class="footer-col">
                    <h3>Contact</h3>
                    <ul>
                        <li><a href="mailto:anton.kergourlay@gmail.com">anton.kergourlay@gmail.com</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-copy">
                <p>© 2026 Anton Kergourlay — Tous droits réservés</p>
                <p><a href="mentions-legales.php" style="color:rgba(255,255,255,0.5);">Mentions légales</a></p>
            </div>
        </div>
    </footer>

    <script src="js/menu.js?v=16"></script>

</body>

</html>
