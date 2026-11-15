// Menu plein écran : ouverture/fermeture, et aperçu du projet survolé.
(function () {
    var hamburger = document.getElementById('hamburger');
    var drawer = document.getElementById('nav-drawer');
    if (!hamburger || !drawer) { return; }
    var nav = document.querySelector('nav');
    var covers = drawer.querySelectorAll('.drawer-preview img');

    function setMenu(open) {
        hamburger.classList.toggle('open', open);
        drawer.classList.toggle('open', open);
        document.body.classList.toggle('menu-open', open);
        document.body.style.overflow = open ? 'hidden' : '';
        hamburger.setAttribute('aria-expanded', open ? 'true' : 'false');
        hamburger.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
        if (open && nav) { nav.classList.remove('nav-hidden'); }
    }

    function showCover(id) {
        covers.forEach(function (img) {
            img.classList.toggle('is-active', img.getAttribute('data-cover') === id);
        });
    }

    hamburger.addEventListener('click', function () {
        setMenu(!drawer.classList.contains('open'));
    });

    // Après un clic sur un lien d'ancre (ou une arrivée avec #section), la barre reste affichée pendant le défilement.
    var lockUntil = 0;
    function keepNav() { lockUntil = Date.now() + 2000; if (nav) { nav.classList.remove('nav-hidden'); } }
    if (window.location.hash) { keepNav(); }
    window.addEventListener('hashchange', keepNav);
    // Tous les liens d'ancre de la page (ex. « Voir les projets ») gardent aussi la barre affichée.
    document.addEventListener('click', function (e) {
        var lien = e.target.closest ? e.target.closest('a[href^="#"]') : null;
        if (lien) { keepNav(); }
    });

    drawer.querySelectorAll('.drawer-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if ((link.getAttribute('href') || '').indexOf('#') !== -1) { keepNav(); }
            setMenu(false);
        });
        var id = link.getAttribute('data-cover');
        if (id) {
            link.addEventListener('mouseenter', function () { showCover(id); });
            link.addEventListener('focus', function () { showCover(id); });
        }
    });

    // Sur l'accueil, le logo remonte en douceur en haut de page au lieu de recharger la page.
    var logo = document.querySelector('.nav-logo a');
    if (logo && document.body.classList.contains('page-accueil')) {
        logo.addEventListener('click', function (e) {
            e.preventDefault();
            setMenu(false);
            window.scrollTo({ top: 0, behavior: 'smooth' });
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, '', window.location.pathname + window.location.search);
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { setMenu(false); }
    });

    // Barre de navigation : elle se cache quand on descend, revient quand on remonte.
    if (nav) {
        var lastY = window.scrollY;
        var hero = document.querySelector('.pj-hero, .hero');
        // Sur une page projet, la barre reste transparente tant qu'elle est au-dessus de la photo d'ouverture.
        function solidFrom() { return hero ? hero.offsetHeight - nav.offsetHeight : 40; }
        function onScroll() {
            var y = window.scrollY;
            nav.classList.toggle('is-scrolled', y > solidFrom());
            if (Date.now() < lockUntil) {
                nav.classList.remove('nav-hidden');
            } else if (!drawer.classList.contains('open')) {
                if (y < 80 || y < lastY - 6) {
                    nav.classList.remove('nav-hidden');
                } else if (y > lastY + 6) {
                    nav.classList.add('nav-hidden');
                }
            }
            lastY = y;
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }
})();
