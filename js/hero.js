// Hero de l'accueil : les couvertures des projets défilent en fondu.
(function () {
    var hero = document.querySelector('.hero');
    var dataEl = document.getElementById('hero-data');
    if (!hero || !dataEl) { return; }
    var projets = JSON.parse(dataEl.textContent);
    var slides = hero.querySelectorAll('.hero-slides img');
    var lien = document.getElementById('hero-current');
    var compteur = lien.querySelector('.hero-count');
    var titre = lien.querySelector('.hero-current-title');
    var courant = 0;
    var minuteur = null;

    function montrer(i) {
        slides[courant].classList.remove('is-active');
        courant = (i + slides.length) % slides.length;
        slides[courant].classList.add('is-active');
        lien.href = 'projet.php?id=' + encodeURIComponent(projets[courant].id);
        compteur.textContent = ('0' + (courant + 1)).slice(-2) + ' / ' + ('0' + slides.length).slice(-2);
        titre.textContent = projets[courant].titre;
    }

    function demarrer() {
        if (minuteur || slides.length < 2) { return; }
        minuteur = setInterval(function () { montrer(courant + 1); }, 5000);
    }
    function arreter() { clearInterval(minuteur); minuteur = null; }

    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }

    // On charge toutes les images une fois la page prête, puis on lance le défilement.
    window.addEventListener('load', function () {
        slides.forEach(function (img) { img.loading = 'eager'; });
        setTimeout(demarrer, 2500);
    });
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) { arreter(); } else if (document.readyState === 'complete') { demarrer(); }
    });
})();
