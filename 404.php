<?php
http_response_code(404);
ini_set('display_errors', 0);
error_reporting(E_ALL);
header("Content-type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Page introuvable | Anton Kergourlay</title>
    <link rel="icon" href="/images/favicon/favicon-aker.svg?v=2" type="image/svg+xml">
    <link rel="icon" href="/images/favicon/favicon.ico?v=2" sizes="32x32">
    <link rel="apple-touch-icon" href="/images/favicon/apple-touch-icon.png?v=2">
    <link rel="stylesheet" href="/css/styles.css?v=126" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=general-sans@300,500,700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://api.fontshare.com/v2/css?f[]=general-sans@300,500,700&display=swap" rel="stylesheet"></noscript>
    <style>
        .legal { max-width: 760px; margin: 0 auto; padding: 8rem 1.25rem 5rem; }
        .legal a.legal-back { font-size: .72rem; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }
        .legal h1 { margin: 1.5rem 0 2.5rem; font-family: var(--titre); font-size: clamp(2.4rem, 7vw, 4rem); font-weight: 700; letter-spacing: -.045em; line-height: 1; }
        .legal h2 { margin: 2.4rem 0 .6rem; font-size: .78rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: var(--blue); }
        .legal p { margin: 0 0 .8rem; }
        .legal a:not(.legal-back) { text-decoration: underline; text-underline-offset: 3px; }
    </style>
</head>

<body>
    <nav>
        <ul>
            <li class="nav-logo"><a href="index.php" aria-label="A.KER, Anton Kergourlay, accueil"><span class="logo-mark">A.KER</span></a></li>
        </ul>
    </nav>

    <main class="legal">
        <a class="legal-back" href="index.php">← Retour au portfolio</a>
        <h1>Page introuvable</h1>
        <p>Cette page n'existe pas ou a été déplacée. Retrouve mes projets sur la <a href="index.php">page d'accueil</a>.</p>
    </main>
</body>

</html>
