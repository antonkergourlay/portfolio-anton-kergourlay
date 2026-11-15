<?php
// Modèles des e-mails envoyés par le formulaire de contact.
// Mise en page en tableaux et styles en ligne : c'est ce que les messageries comprennent le mieux.

// Échappe le texte pour le HTML (apostrophes et guillemets compris).
function h_mail($texte)
{
    return htmlspecialchars($texte, ENT_QUOTES, "UTF-8");
}

// Cadre commun : bandeau sombre avec le logo, carte claire, pied de page discret.
function cadre_mail($titre, $contenuHtml, $pied)
{
    $police = "'Helvetica Neue', Helvetica, Arial, sans-serif";
    return '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>'
        . '<body style="margin:0;padding:0;background:#f3f0e9;">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f0e9;padding:32px 12px;"><tr><td align="center">'
        . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;font-family:' . $police . ';">'
        // bandeau
        . '<tr><td style="background:#171717;padding:26px 32px;border-radius:18px 18px 0 0;">'
        . '<span style="font-size:26px;font-weight:300;letter-spacing:.5px;color:#f4f1ea;">A.KER</span>'
        . '<span style="float:right;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#8c9cff;padding-top:11px;">Portfolio</span>'
        . '</td></tr>'
        // carte
        . '<tr><td style="background:#ffffff;padding:36px 32px 32px;border-radius:0 0 18px 18px;color:#171717;">'
        . '<h1 style="margin:0 0 22px;font-size:24px;line-height:1.2;font-weight:800;letter-spacing:-.5px;color:#171717;">' . $titre . '</h1>'
        . $contenuHtml
        . '</td></tr>'
        // pied
        . '<tr><td style="padding:20px 32px 0;font-size:12px;line-height:1.6;color:#8a8780;text-align:center;">' . $pied . '</td></tr>'
        . '</table></td></tr></table></body></html>';
}

// Une ligne « étiquette / valeur ».
function ligne_mail($etiquette, $valeurHtml)
{
    return '<tr>'
        . '<td style="padding:10px 0;border-top:1px solid #ebe8e1;width:110px;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8780;vertical-align:top;">' . $etiquette . '</td>'
        . '<td style="padding:10px 0;border-top:1px solid #ebe8e1;font-size:15px;line-height:1.5;color:#171717;">' . $valeurHtml . '</td>'
        . '</tr>';
}

// Bloc de citation pour le message.
function citation_mail($messageHtml)
{
    return '<div style="margin:22px 0 4px;padding:18px 20px;background:#f7f5f0;border-left:4px solid #334be8;border-radius:0 12px 12px 0;font-size:15px;line-height:1.65;color:#2b2a27;">' . $messageHtml . '</div>';
}

// Mail reçu par Anton : qui écrit, pour quoi, et le message. On peut répondre directement (Reply-To).
function mail_notification($nom, $email, $sujet, $message)
{
    $n = h_mail($nom);
    $e = h_mail($email);
    $s = h_mail($sujet);
    $contenu = '<p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#4a4944;">Quelqu\'un vient de t\'écrire depuis le formulaire de ton portfolio.</p>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:6px;">'
        . ligne_mail("Nom", '<strong>' . $n . '</strong>')
        . ligne_mail("E-mail", '<a href="mailto:' . $e . '" style="color:#334be8;text-decoration:none;">' . $e . '</a>')
        . ligne_mail("Sujet", $s)
        . '<tr><td colspan="2" style="border-top:1px solid #ebe8e1;font-size:0;line-height:0;height:1px;">&nbsp;</td></tr>'
        . '</table>'
        . citation_mail(nl2br(h_mail($message)))
        . '<table role="presentation" cellpadding="0" cellspacing="0" style="margin-top:26px;"><tr><td style="background:#334be8;border-radius:999px;">'
        . '<a href="mailto:' . $e . '?subject=' . rawurlencode("Re : " . $sujet) . '" style="display:inline-block;padding:13px 26px;font-size:13px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#ffffff;text-decoration:none;">Répondre à ' . $n . '</a>'
        . '</td></tr></table>';
    return cadre_mail("Nouveau message de " . $n, $contenu, "Message reçu via le formulaire de contact du portfolio.<br>Tu peux aussi répondre directement à ce mail.");
}

function mail_notification_texte($nom, $email, $sujet, $message)
{
    return "Nouveau message depuis le portfolio\n\nNom : " . $nom . "\nE-mail : " . $email . "\nSujet : " . $sujet . "\n\n" . $message . "\n";
}

// Mail de confirmation reçu par la personne qui a écrit.
function mail_confirmation($nom, $sujet, $message, $adresseSite)
{
    $n = h_mail($nom);
    $s = h_mail($sujet);
    $contenu = '<p style="margin:0 0 14px;font-size:16px;line-height:1.6;color:#171717;">Bonjour ' . $n . ',</p>'
        . '<p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#4a4944;">Merci pour votre message : il est bien arrivé. Je le lis avec attention et je reviens vers vous dès que possible.</p>'
        . '<p style="margin:26px 0 6px;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8780;">Récapitulatif</p>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0">' . ligne_mail("Sujet", $s) . '<tr><td colspan="2" style="border-top:1px solid #ebe8e1;font-size:0;line-height:0;height:1px;">&nbsp;</td></tr></table>'
        . citation_mail(nl2br(h_mail($message)))
        . '<p style="margin:28px 0 0;font-size:15px;line-height:1.6;color:#171717;">À très bientôt,<br><strong>Anton Kergourlay</strong><br><span style="color:#8a8780;font-size:13px;">Graphisme &amp; création visuelle</span></p>'
        . ($adresseSite !== "" ? '<p style="margin:22px 0 0;font-size:13px;"><a href="' . h_mail($adresseSite) . '" style="color:#334be8;text-decoration:none;">Revoir le portfolio →</a></p>' : '');
    return cadre_mail("Votre message est bien reçu", $contenu, "Ceci est un message automatique : merci de ne pas y répondre.<br>Pour me joindre : anton.kergourlay@gmail.com");
}

function mail_confirmation_texte($nom, $sujet, $message)
{
    return "Bonjour " . $nom . ",\n\nMerci pour votre message : il est bien arrivé. Je reviens vers vous dès que possible.\n\nRécapitulatif\nSujet : " . $sujet . "\n\n" . $message . "\n\nÀ très bientôt,\nAnton Kergourlay\nGraphisme & création visuelle\n";
}
