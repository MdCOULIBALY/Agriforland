<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

function sendHtmlResponse($status, $title, $message, $color) {
    http_response_code($status);
    header('Content-Type: text/html; charset=utf-8');
    
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>$title</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f5f5f5;
                margin: 0;
                padding: 0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                color: #333;
            }
            .response-container {
                background-color: white;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                padding: 30px;
                max-width: 500px;
                text-align: center;
                animation: fadeIn 0.5s ease-in-out;
            }
            .icon {
                font-size: 50px;
                margin-bottom: 20px;
                color: $color;
            }
            h1 {
                margin: 0 0 15px 0;
                color: $color;
            }
            p {
                margin: 0 0 25px 0;
                font-size: 16px;
                line-height: 1.6;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background-color: $color;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                transition: background-color 0.3s;
            }
            .btn:hover {
                background-color: darken($color, 10%);
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body>
        <div class="response-container">
            <div class="icon">{$GLOBALS['icon']}</div>
            <h1>$title</h1>
            <p>$message</p>
            <a href="javascript:history.back()" class="btn">Retour</a>
        </div>
    </body>
    </html>
HTML;
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $message = nl2br(htmlspecialchars(trim($_POST['message'])));

    if ($nom && $prenom && $email && $message) {
        $mail = new PHPMailer(true);

        try {
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host       = 'mail.agriforland.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'info@agriforland.com';
            $mail->Password   = 'Moh@med2904';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            // Expéditeur et destinataires
            $mail->setFrom('info@agriforland.com', 'FORMULAIRE AGRIFORLAND');
            $mail->addAddress('assistance@agriforland.com');


            // Contenu HTML du mail avec design professionnel
            $mail->isHTML(true);
            $mail->Subject = mb_encode_mimeheader('📨 Nouveau message contact - AGRIFORLAND', 'UTF-8');
            $mail->Body    = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message contact</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .email-header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-content {
            padding: 25px;
        }
        .info-block {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .info-block:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .info-label {
            font-weight: 600;
            color: #4CAF50;
            display: block;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
        }
        .message-content {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .email-footer {
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #999;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>📬 Nouveau message de $nom</h1>
        </div>
        
        <div class="email-content">
            <div class="info-block">
                <span class="info-label">Client</span>
                <div class="info-value">$prenom $nom</div>
            </div>
            
            <div class="info-block">
                <span class="info-label">Email</span>
                <div class="info-value">
                    <a href="mailto:$email" style="color: #4CAF50; text-decoration: none;">$email</a>
                </div>
            </div>
            
            <div class="info-block">
                <span class="info-label">Téléphone</span>
                <div class="info-value">$telephone</div>
            </div>
            
            <div class="info-block">
                <span class="info-label">Message</span>
                <div class="message-content">$message</div>
            </div>
        </div>
        
        <div class="email-footer">
            Message envoyé depuis le formulaire de contact AGRIFORLAND - © 
        </div>
    </div>
</body>
</html>
HTML;

            $mail->send();
            $GLOBALS['icon'] = '✓';
            sendHtmlResponse(200, 'Message envoyé !', 'Merci <strong>'.$prenom.' '.$nom.'</strong>, votre message a bien été transmis. Nous vous répondrons dans les plus brefs délais.', '#4CAF50');
            
        } catch (Exception $e) {
            $GLOBALS['icon'] = '⚠️';
            sendHtmlResponse(500, 'Erreur', 'Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer plus tard.', '#FF5722');
        }
    } else {
        $GLOBALS['icon'] = '❌';
        sendHtmlResponse(400, 'Champs manquants', 'Veuillez remplir tous les champs obligatoires du formulaire.', '#F44336');
    }
} else {
    $GLOBALS['icon'] = '🚫';
    sendHtmlResponse(405, 'Méthode non autorisée', 'Cette action n\'est pas permise.', '#9C27B0');
}