<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
header("Content-type: text/html; charset=utf-8");

function retour_formulaire()
{
    header("Location: index.php#contact");
    exit();
}

// Sujets proposés par la liste déroulante du formulaire : aucun autre texte n'est accepté.
$sujetsAutorises = array("Proposition de collaboration", "Projet créatif", "Opportunité de stage", "Autre demande");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    retour_formulaire();
}

// Champ caché « piège » : un humain ne le voit pas, un robot le remplit. On fait semblant que tout va bien.
if (!empty($_POST["site"])) {
    header("Location: index.php?envoye=1#contact");
    exit();
}

$nom = isset($_POST["nom"]) ? trim((string) $_POST["nom"]) : "";
$email = isset($_POST["email"]) ? trim((string) $_POST["email"]) : "";
$sujet = isset($_POST["sujet"]) ? trim((string) $_POST["sujet"]) : "";
$message = isset($_POST["message"]) ? trim((string) $_POST["message"]) : "";

if (mb_strlen($nom) < 2 || mb_strlen($nom) > 60 || preg_match('/[\r\n]/', $nom)) {
    retour_formulaire();
}
if (mb_strlen($message) < 10 || mb_strlen($message) > 2000) {
    retour_formulaire();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 254) {
    retour_formulaire();
}
if (!in_array($sujet, $sujetsAutorises, true)) {
    retour_formulaire();
}

// Limite : 5 envois par heure et par adresse IP, pour que le formulaire ne serve pas à envoyer du spam.
$ip = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "inconnue";
$fichierLimite = sys_get_temp_dir() . "/contact-limite-" . md5($ip);
$maintenant = time();
$envois = array();
if (is_file($fichierLimite)) {
    $lu = json_decode((string) file_get_contents($fichierLimite), true);
    if (is_array($lu)) {
        $envois = array_filter($lu, function ($t) use ($maintenant) {
            return is_int($t) && $t > $maintenant - 3600;
        });
    }
}
if (count($envois) >= 5) {
    http_response_code(429);
    exit("Trop de messages envoyés. Réessaie dans une heure ou écris-moi directement à anton.kergourlay@gmail.com.");
}
$envois[] = $maintenant;
@file_put_contents($fichierLimite, json_encode(array_values($envois)), LOCK_EX);

require(__DIR__ . "/src/PHPMailer.php");
require(__DIR__ . "/src/SMTP.php");
require(__DIR__ . "/src/Exception.php");
require(__DIR__ . "/mail-modeles.php");

$configFichier = __DIR__ . "/config.local.php";
if (!file_exists($configFichier)) {
    http_response_code(500);
    exit("Le formulaire n'est pas configuré.");
}
$config = require($configFichier);
$compteSMTP = $config["compte_smtp"];

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->SMTPDebug = 0;
$mail->isSMTP();
$mail->Host = isset($config["hote_smtp"]) ? $config["hote_smtp"] : "smtp.gmail.com";
$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
$mail->Port = isset($config["port_smtp"]) ? (int) $config["port_smtp"] : 465;
$mail->SMTPAuth = true;
$mail->SMTPKeepAlive = true; // une seule connexion pour les deux e-mails
$mail->Username = $compteSMTP;
$mail->Password = $config["mot_de_passe_application"];
$mail->CharSet = PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;
$mail->isHTML(true);

$mail->setFrom($compteSMTP, "Portfolio - Contact");
$mail->addAddress("anton.kergourlay.etu@univ-lemans.fr", "Anton Kergourlay");
$mail->addAddress("anton.kergourlay.pro@gmail.com", "Anton Kergourlay");
$mail->addReplyTo($email, $nom);
$mail->Subject = "Portfolio · " . $nom . " : " . $sujet;
$mail->Body = mail_notification($nom, $email, $sujet, $message);
$mail->AltBody = mail_notification_texte($nom, $email, $sujet, $message);

if (!$mail->send()) {
    error_log("Formulaire de contact : " . $mail->ErrorInfo);
    echo ("Le message n'a pas pu être envoyé. Écris-moi directement à anton.kergourlay@gmail.com.");
    exit();
}

// Mail de confirmation pour la personne qui a écrit : s'il échoue, le message d'Anton est déjà parti, on note juste l'erreur.
$mail->clearAddresses();
$mail->clearReplyTos();
$mail->setFrom($compteSMTP, "Anton Kergourlay");
$mail->addAddress($email, $nom);
$mail->Subject = "Votre message est bien reçu";
// Adresse du site écrite en dur (et non lue dans l'en-tête Host, que l'envoyeur pourrait falsifier).
$adresseSite = (isset($_SERVER["HTTP_HOST"]) && strpos($_SERVER["HTTP_HOST"], "localhost") === false) ? "https://antonkergourlay.fr/" : "";
$mail->Body = mail_confirmation($nom, $sujet, $message, $adresseSite);
$mail->AltBody = mail_confirmation_texte($nom, $sujet, $message);
if (!$mail->send()) {
    error_log("Formulaire de contact (confirmation) : " . $mail->ErrorInfo);
}
$mail->smtpClose();

/* Redirection avec paramètre envoye=1 pour afficher le message de confirmation */
header("Location: index.php?envoye=1#contact");
exit();
