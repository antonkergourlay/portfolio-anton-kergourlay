<?php
require_once __DIR__ . "/social.php";
// Menu plein écran. Attend $menuProjets (id, titre, sous_titre, cover, pos) et $menuPrefix ("" sur l'accueil, "index.php" ailleurs).
?>
    <div class="nav-drawer" id="nav-drawer">
        <div class="drawer-main">
            <p class="drawer-kicker">Projets <span><?php echo str_pad(count($menuProjets), 2, "0", STR_PAD_LEFT); ?></span></p>
            <ol class="drawer-projects">
                <?php foreach ($menuProjets as $i => $p) { ?>
                    <li>
                        <a class="drawer-link" style="--i: <?php echo $i; ?>" data-cover="<?php echo htmlspecialchars($p["id"]); ?>" href="projet.php?id=<?php echo urlencode($p["id"]); ?>">
                            <small><?php echo str_pad($i + 1, 2, "0", STR_PAD_LEFT); ?></small>
                            <span class="dl-title"><?php echo htmlspecialchars($p["titre"]); ?></span>
                            <span class="dl-meta"><?php echo htmlspecialchars($p["sous_titre"]); ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ol>
            <div class="drawer-bottom">
                <div class="drawer-secondary">
                    <a class="drawer-link drawer-small" style="--i: <?php echo count($menuProjets); ?>" href="<?php echo $menuPrefix; ?>#galerie">Tous les projets</a>
                    <a class="drawer-link drawer-small" style="--i: <?php echo count($menuProjets) + 1; ?>" href="<?php echo $menuPrefix; ?>#apropos">À propos</a>
                    <a class="drawer-link drawer-small" style="--i: <?php echo count($menuProjets) + 2; ?>" href="<?php echo $menuPrefix; ?>#contact">Contact</a>
                </div>
                <div class="drawer-foot">
                    <?php echo liens_reseaux("on-dark"); ?>
                    <?php echo bouton_cv("on-dark"); ?>
                    <a class="drawer-mail" href="mailto:anton.kergourlay@gmail.com">anton.kergourlay@gmail.com</a>
                </div>
            </div>
        </div>
        <div class="drawer-preview" aria-hidden="true">
            <?php foreach ($menuProjets as $i => $p) { ?>
                <img class="<?php echo $i === 0 ? "is-active" : ""; ?>" data-cover="<?php echo htmlspecialchars($p["id"]); ?>" src="<?php echo htmlspecialchars($p["cover"]); ?>" alt="" loading="lazy" style="--pos: <?php echo htmlspecialchars($p["pos"]); ?>">
            <?php } ?>
        </div>
    </div>
