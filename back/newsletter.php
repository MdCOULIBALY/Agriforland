<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST['newsletter_email']) ? htmlspecialchars(trim($_POST['newsletter_email'])) : '';

    if ($email) {
        $mail = new PHPMailer(true);

        try {
            $mail->CharSet = 'UTF-8';

            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host       = 'mail.agriforland.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'info@agriforland.com';
            $mail->Password   = 'Moh@med2904'; // Mot de passe réel
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            // Expéditeur et destinataire
            $mail->setFrom('info@agriforland.com', 'Newsletter Agriforland');
            $mail->addAddress('assistance@agriforland.com');// Tu reçois les inscriptions
            
            // Contenu
            $mail->isHTML(true);
            $mail->Subject = 'Nouvelle inscription à la newsletter';
            $mail->Body = '
            <div style="font-family: Arial, sans-serif; background-color: #f6ffde; padding: 30px; border-radius: 10px; max-width: 600px; margin: auto; border: 1px solid #a9cf46;">
              <h2 style="color: #4CAF50; text-align: center;">
                <img src="https://cdn-icons-png.flaticon.com/512/561/561127.png" width="24" style="vertical-align: middle; margin-right: 10px;">
                Nouvelle inscription à la newsletter
              </h2>
          
              <p style="font-size: 16px; color: #333;">Une nouvelle personne s’est inscrite à la newsletter du site <strong>Agriforland</strong> :</p>
          
              <div style="background-color: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; margin-top: 15px;">
                <p style="font-size: 18px; color: #000;">
                  <img src="https://cdn-icons-png.flaticon.com/512/561/561127.png" width="20" style="vertical-align: middle; margin-right: 8px;">
                  <strong>Email :</strong> ' . $email . '
                </p>
              </div>
          
              <p style="margin-top: 30px; font-size: 14px; color: #888;">
                Cet email a été généré automatiquement depuis le site 
                <a href="https://www.agriforland.com" style="color: #4CAF50; text-decoration: none;">agriforland.com</a>.
              </p>
            </div>';
          
            $mail->send();
            http_response_code(200);
            echo "Inscription enregistrée.";
        } catch (Exception $e) {
            http_response_code(500);
            echo "Erreur lors de l'envoi : " . $mail->ErrorInfo;
        }
    } else {
        http_response_code(400);
        echo "Email invalide.";
    }
} else {
    http_response_code(405);
    echo "Méthode non autorisée.";
}