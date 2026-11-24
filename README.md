# Portfolio d'Anton Kergourlay

Portfolio de graphiste, étudiant en BUT MMI (Métiers du Multimédia et de l'Internet). En ligne sur https://antonkergourlay.fr.

Le site est écrit en PHP, HTML, CSS et JavaScript, sans framework, sans build et sans Composer.

## Lancer le site en local

```bash
php -S localhost:8123
```

Puis ouvrir http://localhost:8123.

## Structure

- `index.php` : accueil, galerie, à propos et contact.
- `projet.php?id=<id>` : fiche d'un projet.
- `contact.php` : traitement du formulaire de contact.
- `data/projets/<id>.csv` : contenu de chaque projet (séparateur `;`).
- `css/styles.css`, `js/` : styles et scripts.
- `src/` : PHPMailer, pour l'envoi des e-mails.

## Formulaire de contact

L'envoi passe par SMTP. Pour l'activer en local :

1. Copier `config.example.php` en `config.local.php`.
2. Renseigner l'adresse du compte et son mot de passe d'application.

`config.local.php` est ignoré par git : il ne doit jamais être commité.

## Droits

Textes, images et projets : © Anton Kergourlay, tous droits réservés. PHPMailer (`src/`) est distribué sous sa propre licence (LGPL).
