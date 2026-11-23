<?php
require_once __DIR__ . "/social.php";
// Adresse absolue du site, calculée automatiquement (utile pour les images d'aperçu des réseaux sociaux).
$schema = (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") ? "https" : "http";
$baseUrl = $schema . "://" . (isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "localhost") . rtrim(dirname($_SERVER["SCRIPT_NAME"]), "/\\") . "/";

// Projets affichés sur l'accueil. Pour ajouter un projet : déposer images/projets/<id>/cover-wide.webp puis ajouter une ligne ici.
// "actif" => false masque un projet sans rien supprimer. "pos" règle le cadrage de la couverture (x y).
$projetsAccueil = [
    ["id" => "zest", "titre" => "ZEST", "sous_titre" => "Packaging · 2026", "alt" => "Canettes ZEST en motif sur fond vert", "pos" => "50% 50%", "actif" => true],
    ["id" => "tour-90-couleurs", "titre" => "Tour du monde en 90 couleurs", "sous_titre" => "Peinture · Dessin · 2024–2025", "alt" => "Quatre œuvres exposées sur des panneaux dans la rue", "pos" => "50% 50%", "actif" => true],
    ["id" => "magazine-urasawa", "titre" => "Magazine – Naoki Urasawa", "sous_titre" => "Édition · 2026", "alt" => "Double page du magazine Naoki Urasawa ouverte sur une table", "pos" => "50% 70%", "actif" => true],
    ["id" => "flyer-trail-croix", "titre" => "Flyer – Trail de la Croix", "sous_titre" => "Affiche · 2025", "alt" => "Affiches du Trail de la Croix collées sur un mur de briques", "pos" => "50% 45%", "actif" => true],
    ["id" => "menace-santana", "titre" => "Univers menace Santana", "sous_titre" => "Projet personnel · 2022–2024", "alt" => "Quatre masques menace Santana sur fond noir", "pos" => "50% 50%", "actif" => true],
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Anton Kergourlay, graphiste et étudiant en BUT MMI : portfolio de projets en édition, packaging, identité visuelle, affiche et web.">
    <link rel="canonical" href="https://antonkergourlay.fr/">
    <meta property="og:site_name" content="Anton Kergourlay">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://antonkergourlay.fr/">
    <meta property="og:title" content="Anton Kergourlay | Graphiste, portfolio BUT MMI">
    <meta property="og:description" content="Portfolio d'Anton Kergourlay, graphiste et étudiant en BUT MMI : édition, packaging, identité visuelle, affiche et web.">
    <meta property="og:image" content="https://antonkergourlay.fr/images/previews/preview-portfolio.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Anton Kergourlay | Graphiste, portfolio BUT MMI">
    <meta name="twitter:description" content="Portfolio d'Anton Kergourlay, graphiste et étudiant en BUT MMI : édition, packaging, identité visuelle, affiche et web.">
    <meta name="twitter:image" content="https://antonkergourlay.fr/images/previews/preview-portfolio.jpg">
    <title>Anton Kergourlay | Graphiste, portfolio BUT MMI</title>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "WebSite",
                "name": "Anton Kergourlay",
                "url": "https://antonkergourlay.fr/",
                "inLanguage": "fr"
            },
            {
                "@type": "Person",
                "name": "Anton Kergourlay",
                "url": "https://antonkergourlay.fr/",
                "jobTitle": "Graphiste, étudiant en BUT MMI",
                "knowsAbout": ["Graphisme", "Design éditorial", "Packaging", "Identité visuelle", "Création web"],
                "image": "https://antonkergourlay.fr/images/previews/preview-portfolio.jpg",
                "sameAs": <?php echo json_encode(array_values(array_filter(array("https://www.linkedin.com/in/anton-kergourlay-06a394317", LIEN_GITHUB, LIEN_INSTAGRAM))), JSON_UNESCAPED_SLASHES); ?>
            }
        ]
    }
    </script>
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

    <style>
        /* ANIMATION ENVELOPPE */
        #send-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: #f5f3ee;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }

        #send-overlay.visible {
            opacity: 1;
            pointer-events: all;
        }

        #send-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        #send-svg {
            width: 160px;
            height: 110px;
            overflow: visible;
            transform-origin: center center;
        }

        #env-lid {
            transform-origin: 80px 0px;
        }

        #send-label {
            font-family: var(--titre);
            font-style: normal; font-weight: 500;
            font-size: 0.88rem;
            color: rgba(15, 15, 15, 0.55);
            display: flex;
            align-items: center;
            gap: 0.35rem;
            letter-spacing: 0.03em;
            opacity: 0;
        }

        .send-dots span {
            display: inline-block;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #3548e8;
            margin: 0 2px;
            opacity: 0;
        }

        .send-dots.active span:nth-child(1) {
            animation: sdot 0.65s 0s infinite;
        }

        .send-dots.active span:nth-child(2) {
            animation: sdot 0.65s 0.1s infinite;
        }

        .send-dots.active span:nth-child(3) {
            animation: sdot 0.65s 0.2s infinite;
        }

        @keyframes sdot {

            0%,
            80%,
            100% {
                transform: translateY(0);
                opacity: 0.25;
            }

            40% {
                transform: translateY(-5px);
                opacity: 1;
            }
        }
    </style>
</head>

<body class="page-accueil">

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
    foreach ($projetsAccueil as $p) {
        if ($p["actif"]) {
            $menuProjets[] = array("id" => $p["id"], "titre" => $p["titre"], "sous_titre" => $p["sous_titre"], "cover" => "./images/projets/" . $p["id"] . "/cover-wide.webp", "pos" => $p["pos"]);
        }
    }
    $menuPrefix = "";
    include "menu.php";
    ?>

    <!-- HERO -->
    <header class="hero">
        <div class="hero-slides" aria-hidden="true">
            <?php foreach ($menuProjets as $i => $p) { $tall = "./images/projets/" . $p["id"] . "/cover-tall.webp"; ?>
                <picture>
                    <?php if (file_exists($tall)) { ?><source media="(max-width: 700px)" srcset="<?php echo htmlspecialchars($tall); ?>"><?php } ?>
                    <img class="<?php echo $i === 0 ? "is-active" : ""; ?>" src="<?php echo htmlspecialchars($p["cover"]); ?>" alt="" <?php echo $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"'; ?> style="--pos: <?php echo htmlspecialchars($p["pos"]); ?>">
                </picture>
            <?php } ?>
        </div>
        <div class="hero-main">
            <h1>Anton<br><em>Kergourlay</em></h1>
            <p class="hero-tag">Graphisme &amp; création visuelle · Étudiant en BUT MMI à Laval</p>
        </div>
        <div class="hero-bottom">
            <a class="hero-current" id="hero-current" href="projet.php?id=<?php echo urlencode($menuProjets[0]["id"]); ?>">
                <span class="hero-count">01 / <?php echo str_pad(count($menuProjets), 2, "0", STR_PAD_LEFT); ?></span>
                <span class="hero-current-title"><?php echo htmlspecialchars($menuProjets[0]["titre"]); ?></span>
            </a>
            <a class="hero-cta" href="#galerie">Voir les projets <span>↓</span></a>
        </div>
        <script type="application/json" id="hero-data"><?php echo json_encode(array_map(function ($p) { return array("id" => $p["id"], "titre" => $p["titre"]); }, $menuProjets), JSON_UNESCAPED_UNICODE); ?></script>
    </header>

    <main>

        <!-- GALERIE -->
        <section id="galerie" aria-labelledby="titre-galerie">
            <h2 id="titre-galerie" class="section-label">Projets</h2>

            <div class="gallery-grid">
                <?php foreach ($projetsAccueil as $p) { if (!$p["actif"]) { continue; } ?>
                    <a class="proj-card" href="projet.php?id=<?php echo urlencode($p["id"]); ?>" data-project-id="<?php echo htmlspecialchars($p["id"]); ?>" style="--pos: <?php echo htmlspecialchars($p["pos"]); ?>">
                        <img src="./images/projets/<?php echo htmlspecialchars($p["id"]); ?>/cover-wide.webp" alt="<?php echo htmlspecialchars($p["alt"]); ?>" loading="lazy">
                        <div class="proj-info">
                            <div class="proj-title"><?php echo htmlspecialchars($p["titre"]); ?></div>
                            <div class="proj-sub"><?php echo htmlspecialchars($p["sous_titre"]); ?></div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </section>

    </main>

    <!-- À PROPOS -->
    <div class="about-section-wrap">
        <div style="max-width:1400px; margin:0 auto; padding:0 3rem;">
            <section id="apropos" aria-labelledby="titre-apropos">
                <h2 id="titre-apropos" class="section-label">À propos</h2>
                <figure class="about-photo">
                    <img src="./images/a-propos/photo-anton.webp" alt="Portrait d'Anton Kergourlay" loading="lazy">
                </figure>
                <div class="about">
                    <div class="about-text">
                        <p>Je suis Anton Kergourlay, étudiant en deuxième année de BUT MMI à Laval. Je dessine, je peins et je fais du modelage, et je m’intéresse surtout aux univers visuels qui entourent la musique, en particulier le rap : pochettes, clips, identités, concerts. C’est dans ce domaine que je veux travailler.</p>
                        <p>Je m’intéresse aussi beaucoup à l’IA, que j’essaie de comprendre pour m’en servir correctement. Je cherche un stage de 10 semaines, du 22 février au 30 avril 2027.</p>
                        <p class="about-more">Mes influences : SCH, PNL, Bello Dallas, Norman Rockwell, Metanoia, Mickael Azules, Holow, Dexter Maurer.</p>
                        <dl class="about-skills">
                            <div><dt>Design graphique</dt><dd><span class="sk-tools">Affinity, Figma</span><span class="sk-what">Identité visuelle, charte graphique, typographie, photomontage, mockups</span></dd></div>
                            <div><dt>Print</dt><dd><span class="sk-tools">Affinity Publisher</span><span class="sk-what">Mise en page, affiche, flyer, packaging, préparation des fichiers d’impression</span></dd></div>
                            <div><dt>Audiovisuel</dt><dd><span class="sk-tools">DaVinci Resolve, CapCut</span><span class="sk-what">Prise de son, montage vidéo et sonore</span></dd></div>
                            <div><dt>Création manuelle</dt><dd><span class="sk-tools">Dessin, peinture, modelage</span></dd></div>
                            <div><dt>Web</dt><dd><span class="sk-tools">HTML / CSS, PHP</span><span class="sk-what">J’ai déjà vendu des sites web</span></dd></div>
                        </dl>
                    </div>
                </div>
            </section>
        </div>
    </div>


    <!-- CONTACT -->
    <div class="contact-section-wrap">
        <div style="max-width:1400px; margin:0 auto; padding:0 3rem;">
            <section id="contact" class="contact-section" aria-labelledby="titre-contact">
                <h2 id="titre-contact" class="section-label">Contact</h2>
                <h2 class="contact-headline">Travaillons<br><em>ensemble.</em></h2>
                <div class="contact-grid">
                    <div class="contact-info">
                        <p>Une idée, un projet, un stage ?<br>Écrivez-moi, je réponds rapidement.</p>
                        <br>
                        <p><a href="mailto:anton.kergourlay@gmail.com">anton.kergourlay@gmail.com</a></p>
                        <?php $cv = bouton_cv(); if ($cv !== "") { echo '<div class="contact-cv">' . $cv . '</div>'; } ?>
                        <div class="contact-actions"><?php echo liens_reseaux(); ?></div>
                    </div>
                    <form class="contact-form" id="contact-form" action="contact.php" method="post">
                        <div id="form-success" class="form-ok" role="status" hidden>
                            <span class="form-ok-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
                            </span>
                            <h3 class="form-ok-title">Message envoyé.</h3>
                            <p class="form-ok-text">Merci, je vous réponds dès que possible. Un récapitulatif vient d’arriver dans votre boîte mail.</p>
                            <button type="button" class="form-ok-btn" id="form-again">Écrire un autre message</button>
                        </div>
                        <div class="cf-row">
                            <input class="cf-input" type="text" name="nom" required minlength="2" maxlength="60" placeholder="Votre nom">
                            <input class="cf-input" type="email" name="email" required placeholder="votre@email.com">
                        </div>
                        <select class="cf-input" name="sujet" aria-label="Sujet" required>
                            <option value="" selected disabled>Sujet</option>
                            <option value="Proposition de collaboration">Proposition de collaboration</option>
                            <option value="Projet créatif">Projet créatif</option>
                            <option value="Opportunité de stage">Opportunité de stage ou d'alternance</option>
                            <option value="Autre demande">Autre demande</option>
                        </select>
                        <input type="text" name="site" value="" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0">
                        <textarea class="cf-input" name="message" required minlength="10" maxlength="2000" rows="4" placeholder="Dites-moi en plus…"></textarea>
                        <button class="cf-btn" type="submit">Envoyer →</button>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <!-- ANIMATION ENVELOPPE ENVOI -->
    <div id="send-overlay" aria-hidden="true">
        <div id="send-inner">
            <svg id="send-svg" viewBox="0 0 160 110" xmlns="http://www.w3.org/2000/svg">
                <rect x="0" y="0" width="160" height="110" rx="5" fill="#3548e8" />
                <polygon points="0,110 80,64 160,110" fill="#2638c9" />
                <line x1="0" y1="0" x2="74" y2="56" stroke="#2638c9" stroke-width="1" opacity="0.6" />
                <line x1="160" y1="0" x2="86" y2="56" stroke="#2638c9" stroke-width="1" opacity="0.6" />
                <rect x="48" y="32" width="64" height="44" rx="3" fill="white" opacity="0.15" />
                <line x1="56" y1="45" x2="104" y2="45" stroke="white" stroke-width="1.8" opacity="0.5" stroke-linecap="round" />
                <line x1="56" y1="54" x2="104" y2="54" stroke="white" stroke-width="1.8" opacity="0.32" stroke-linecap="round" />
                <line x1="56" y1="63" x2="88" y2="63" stroke="white" stroke-width="1.8" opacity="0.2" stroke-linecap="round" />
                <polygon id="env-lid" points="0,0 80,57 160,0" fill="#3548e8" stroke="#2638c9" stroke-width="1.5" stroke-linejoin="round" />
            </svg>
            <div id="send-label">
                <span id="send-text"></span>
                <span class="send-dots"><span></span><span></span><span></span></span>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="footer-inner">
            <div class="footer-columns">
                <div class="footer-brand">
                    <p>Anton <em>Kergourlay</em></p>
                    <span>Étudiant en BUT MMI — IUT Laval<br>Graphisme &amp; création visuelle</span>
                </div>
                <div class="footer-col">
                    <h3>Compétences</h3>
                    <ul>
                        <li>Design graphique</li>
                        <li>Print</li>
                        <li>Audiovisuel</li>
                        <li>Web</li>
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

    <script>
        // Animation enveloppe + soumission formulaire
        (function() {
            function easeInOut(t) {
                return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
            }

            function easeIn(t) {
                return t * t * t;
            }

            function tween(duration, ease, onUpdate, onDone) {
                var start = performance.now();

                function step(now) {
                    var raw = Math.min((now - start) / duration, 1);
                    onUpdate(ease(raw));
                    if (raw < 1) requestAnimationFrame(step);
                    else if (onDone) onDone();
                }
                requestAnimationFrame(step);
            }

            function lerp(a, b, t) {
                return a + (b - a) * t;
            }

            function typeText(el, text, speed, done) {
                el.textContent = '';
                var i = 0;
                var iv = setInterval(function() {
                    el.textContent += text[i];
                    i++;
                    if (i >= text.length) {
                        clearInterval(iv);
                        if (done) done();
                    }
                }, speed);
            }

            function showEnvelope() {
                var overlay = document.getElementById('send-overlay');
                var svg = document.getElementById('send-svg');
                var lid = document.getElementById('env-lid');
                var label = document.getElementById('send-label');
                var textEl = document.getElementById('send-text');
                var dots = document.querySelector('.send-dots');

                overlay.classList.add('visible');

                // Rabat se ferme en 280ms
                tween(280, easeInOut, function(t) {
                    lid.style.transform = 'rotateX(' + lerp(0, 180, t) + 'deg)';
                }, function() {
                    // Texte + dots apparaissent
                    label.style.transition = 'opacity 0.15s';
                    label.style.opacity = '1';
                    textEl.textContent = 'Envoi en cours';
                    dots.classList.add('active');

                    // Légère oscillation en boucle pendant le chargement
                    (function wobble() {
                        tween(600, easeInOut, function(t) {
                            svg.style.transform = 'translateY(' + (Math.sin(t * Math.PI * 2) * 4) + 'px)';
                        }, wobble);
                    })();
                });
            }

            // Branchement : le formulaire soumet normalement, l'animation est un habillage
            var form = document.getElementById('contact-form');
            if (form) {
                form.addEventListener('submit', function() {
                    showEnvelope();
                    // Pas de preventDefault — la page part immédiatement
                });
            }

            // Affichage confirmation après envoi : la carte remplace le formulaire
            var params = new URLSearchParams(window.location.search);
            if (params.get('envoye') === '1') {
                var msg = document.getElementById('form-success');
                var formEl = document.getElementById('contact-form');
                if (msg && formEl) {
                    formEl.classList.add('is-sent');
                    msg.hidden = false;
                    var again = document.getElementById('form-again');
                    if (again) {
                        again.addEventListener('click', function() {
                            formEl.classList.remove('is-sent');
                            msg.hidden = true;
                            var premier = formEl.querySelector('input');
                            if (premier) premier.focus();
                        });
                    }
                }
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({}, '', window.location.pathname + '#contact');
                }
            }
        })();
    </script>
    <script src="js/menu.js?v=16"></script>
    <script src="js/hero.js"></script>
</body>

</html>
