<?php
require_once '../back/phpmailer/src/Exception.php';
require_once '../back/phpmailer/src/PHPMailer.php';
require_once '../back/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Configuration SMTP
define('SMTP_HOST', 'mail.agriforland.com');
define('SMTP_USERNAME', 'info@agriforland.com');
define('SMTP_PASSWORD', 'Moh@med2904');
define('SMTP_ENCRYPTION', 'ssl');
define('SMTP_PORT', 465);
define('CONTACT_EMAIL', 'ranch@agriforland.com');

// Traitement du formulaire
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'submit_order') {
    
    $nom = htmlspecialchars($_POST['nom'] ?? '');
    $telephone = htmlspecialchars($_POST['telephone'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $commune = htmlspecialchars($_POST['commune'] ?? '');
    $adresse = htmlspecialchars($_POST['adresse'] ?? '');
    
    $poulet_1kg = intval($_POST['qty_poulet_1kg'] ?? 0);
    $poulet_13kg = intval($_POST['qty_poulet_13kg'] ?? 0);
    $poulet_16kg = intval($_POST['qty_poulet_16kg'] ?? 0);
    $poulet_2kg = intval($_POST['qty_poulet_2kg'] ?? 0);
    $poulet_26kg = intval($_POST['qty_poulet_26kg'] ?? 0);
    
    $preparation = htmlspecialchars($_POST['preparation'] ?? '');
    $emballage = htmlspecialchars($_POST['emballage'] ?? '');
    $mode_livraison = htmlspecialchars($_POST['mode_livraison'] ?? '');
    $date_livraison = htmlspecialchars($_POST['date_livraison'] ?? '');
    $heure_livraison = htmlspecialchars($_POST['heure_livraison'] ?? '');
    $mode_paiement = htmlspecialchars($_POST['mode_paiement'] ?? '');
    $moyen_paiement = htmlspecialchars($_POST['moyen_paiement'] ?? '');
    $instructions = htmlspecialchars($_POST['instructions'] ?? '');
    
    $total_produits = ($poulet_1kg * 3000) + ($poulet_13kg * 4500) + ($poulet_16kg * 5500) + ($poulet_2kg * 7500) + ($poulet_26kg * 8500);
    $total_qty = $poulet_1kg + $poulet_13kg + $poulet_16kg + $poulet_2kg + $poulet_26kg;
    
    $remise = 0;
    if ($total_qty >= 5) {
        $remise = $total_produits * 0.10; // 10% au lieu de 25%
    }
    
    $frais_emballage = 0;
    if ($emballage === 'premium') $frais_emballage = $total_qty * 500;
    if ($emballage === 'cadeau') $frais_emballage = $total_qty * 1000;
    
    $frais_livraison = 0;
    if ($mode_livraison === 'livraison') {
        if ($commune === 'cocody') {
            $frais_livraison = 1500;
        } elseif ($commune === 'dimbokro') {
            $frais_livraison = 500;
        } else {
            $frais_livraison = 2000; // autres communes
        }
        
        if (($total_produits - $remise + $frais_emballage) >= 50000) {
            $frais_livraison = 0;
        }
    } elseif ($mode_livraison === 'expedition') {
        $frais_livraison = 3000;
    }
    
    $total_final = $total_produits - $remise + $frais_emballage + $frais_livraison;
    $acompte = $total_final * 0.5;
    
    $numeros_paiement = [
        'mtn-money' => '05 55 95 06 08',
        'orange-money' => '07 48 11 39 65',
        'wave' => '05 06 06 45 95'
    ];
    
    // Emails (même logique que l'original)
    $mail = new PHPMailer(true);
    
    try {
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Options SMTP supplémentaires
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Timeout plus long
        $mail->Timeout = 60;
        
        // Calcul des totaux pour l'affichage
        $sous_total_produits = ($poulet_1kg * 3000) + ($poulet_13kg * 4500) + ($poulet_16kg * 5500) + ($poulet_2kg * 7500) + ($poulet_26kg * 8500);
        $remise_qty = ($total_qty >= 5) ? ($sous_total_produits * 0.10) : 0;
        $frais_emballage_display = 0;
        if ($emballage === 'premium') $frais_emballage_display = $total_qty * 500;
        if ($emballage === 'cadeau') $frais_emballage_display = $total_qty * 1000;
        
        $frais_livraison_display = 0;
        if ($mode_livraison === 'livraison') {
            if ($commune === 'cocody') {
                $frais_livraison_display = 1500;
            } elseif ($commune === 'dimbokro') {
                $frais_livraison_display = 500;
            } else {
                $frais_livraison_display = 2000;
            }
            if (($sous_total_produits - $remise_qty + $frais_emballage_display) >= 50000) {
                $frais_livraison_display = 0;
            }
        } elseif ($mode_livraison === 'expedition') {
            $frais_livraison_display = 3000;
        }
        
        $remise_paiement = ($mode_paiement === 'integral') ? ($total_final * 0.02) : 0;
        $acompte_display = ($mode_paiement === 'acompte') ? ($total_final * 0.5) : 0;
        
        // Template CSS pour les emails
        $emailCSS = "
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; margin: 0; padding: 0; background-color: #f6ffde; }
            .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #a9cf46 0%, #759916 100%); color: white; text-align: center; padding: 30px 20px; }
            .header h1 { margin: 0; font-size: 28px; font-weight: bold; }
            .header p { margin: 10px 0 0 0; opacity: 0.9; font-size: 16px; }
            .content { padding: 30px; }
            .greeting { font-size: 18px; color: #2c541d; margin-bottom: 20px; }
            .section { margin-bottom: 25px; }
            .section h3 { color: #759916; border-bottom: 2px solid #a9cf46; padding-bottom: 8px; margin-bottom: 15px; }
            .order-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            .order-table th { background: #f6ffde; padding: 12px; text-align: left; border: 1px solid #e5e7eb; font-weight: bold; color: #2c541d; }
            .order-table td { padding: 10px 12px; border: 1px solid #e5e7eb; }
            .total-row { background: #f6ffde; font-weight: bold; color: #2c541d; }
            .info-box { background: #e8f4fd; border: 1px solid #bee5eb; border-radius: 8px; padding: 15px; margin: 15px 0; }
            .info-box h4 { color: #2980b9; margin-top: 0; }
            .contact-info { background: #f8f9fa; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0; }
            .contact-info h4 { color: #759916; margin-bottom: 15px; }
            .contact-item { margin: 8px 0; color: #666; }
            .footer { background: #2c541d; color: white; text-align: center; padding: 20px; }
            .footer p { margin: 5px 0; }
            .highlight { color: #e74c3c; font-weight: bold; }
            .success { color: #27ae60; font-weight: bold; }
        </style>";
        
        // EMAIL AU CLIENT
        $mail->setFrom(SMTP_USERNAME, 'AGRIFORLAND RANCH');
        $mail->addAddress($email, $nom);
        $mail->addReplyTo(CONTACT_EMAIL, 'Assistance AGRIFORLAND');
        
        $mail->isHTML(true);
        $mail->Subject = 'Confirmation de commande - Poulets des Fêtes 2025 🐓';
        
        // Construction du tableau de commande pour le client
        $commande_details = '';
        if ($poulet_1kg > 0) $commande_details .= "<tr><td>Poulets  (1-1,2kg)</td><td style='text-align:center'>$poulet_1kg</td><td style='text-align:right'>" . number_format($poulet_1kg * 3000, 0, ',', ' ') . " FCFA</td></tr>";
        if ($poulet_13kg > 0) $commande_details .= "<tr><td>Poulets  (1,3-1,5kg)</td><td style='text-align:center'>$poulet_13kg</td><td style='text-align:right'>" . number_format($poulet_13kg * 4500, 0, ',', ' ') . " FCFA</td></tr>";
        if ($poulet_16kg > 0) $commande_details .= "<tr><td>Poulets  (1,6-1,9kg)</td><td style='text-align:center'>$poulet_16kg</td><td style='text-align:right'>" . number_format($poulet_16kg * 5500, 0, ',', ' ') . " FCFA</td></tr>";
        if ($poulet_2kg > 0) $commande_details .= "<tr><td>Poulets  (2-2,5kg)</td><td style='text-align:center'>$poulet_2kg</td><td style='text-align:right'>" . number_format($poulet_2kg * 7500, 0, ',', ' ') . " FCFA</td></tr>";
        if ($poulet_26kg > 0) $commande_details .= "<tr><td>Poulets  (+2,6kg)</td><td style='text-align:center'>$poulet_26kg</td><td style='text-align:right'>" . number_format($poulet_26kg * 8500, 0, ',', ' ') . " FCFA</td></tr>";
        
        // Calculs d'affichage
        $calculs_details = '';
        $calculs_details .= "<tr><td colspan='2'><strong>Sous-total</strong></td><td style='text-align:right'><strong>" . number_format($sous_total_produits, 0, ',', ' ') . " FCFA</strong></td></tr>";
        
        if ($remise_qty > 0) {
            $calculs_details .= "<tr style='color: #27ae60;'><td colspan='2'>Remise -10% (5+ poulets)</td><td style='text-align:right'>-" . number_format($remise_qty, 0, ',', ' ') . " FCFA</td></tr>";
        }
        
        if ($frais_emballage_display > 0) {
            $emballage_text = ($emballage === 'premium') ? 'Emballage Premium' : 'Emballage Cadeau';
            $calculs_details .= "<tr><td colspan='2'>$emballage_text</td><td style='text-align:right'>+" . number_format($frais_emballage_display, 0, ',', ' ') . " FCFA</td></tr>";
        }
        
        if ($frais_livraison_display > 0) {
            $livraison_text = ($mode_livraison === 'expedition') ? 'Frais d\'expédition' : 'Frais de livraison';
            $calculs_details .= "<tr><td colspan='2'>$livraison_text</td><td style='text-align:right'>+" . number_format($frais_livraison_display, 0, ',', ' ') . " FCFA</td></tr>";
        } elseif ($mode_livraison === 'livraison' && $frais_livraison_display === 0) {
            $calculs_details .= "<tr style='color: #27ae60;'><td colspan='2'>Livraison gratuite (commande > 50k)</td><td style='text-align:right'>0 FCFA</td></tr>";
        }
        
        if ($remise_paiement > 0) {
            $calculs_details .= "<tr style='color: #27ae60;'><td colspan='2'>Remise paiement intégral (-2%)</td><td style='text-align:right'>-" . number_format($remise_paiement, 0, ',', ' ') . " FCFA</td></tr>";
        }
        
        // Informations de livraison
        $livraison_info = '';
        if ($mode_livraison === 'livraison') {
            $commune_text = '';
            if ($commune === 'cocody') {
                $commune_text = 'Cocody';
            } elseif ($commune === 'dimbokro') {
                $commune_text = 'Dimbokro';
            } else {
                $commune_text = 'Autres communes';
            }
            $livraison_info = "<p><strong>Mode :</strong> Livraison à domicile ($commune_text)<br>";
            $livraison_info .= "<strong>Adresse :</strong> " . nl2br(htmlspecialchars($adresse)) . "<br>";
        } elseif ($mode_livraison === 'expedition') {
            $livraison_info = "<p><strong>Mode :</strong> Expédition<br>";
            $livraison_info .= "<strong>Adresse :</strong> " . nl2br(htmlspecialchars($adresse)) . "<br>";
        } else {
            $livraison_info = "<p><strong>Mode :</strong> Retrait à la ferme<br>";
        }
        $livraison_info .= "<strong>Date souhaitée :</strong> " . date('d/m/Y', strtotime($date_livraison)) . "<br>";
        if ($heure_livraison) $livraison_info .= "<strong>Créneau :</strong> $heure_livraison<br>";
        $livraison_info .= "<strong>Préparation :</strong> $preparation</p>";
        
        // Informations de paiement
        $paiement_info = '';
        if ($mode_paiement === 'acompte') {
            $paiement_info = "<p class='highlight'>Acompte à payer (50%) : " . number_format($acompte_display, 0, ',', ' ') . " FCFA</p>";
        } elseif ($mode_paiement === 'integral') {
            $paiement_info = "<p class='success'>Paiement intégral (avec remise -2%)</p>";
        } else {
            $paiement_info = "<p>Paiement à la livraison</p>";
        }
        
        if ($moyen_paiement && in_array($moyen_paiement, ['orange-money', 'mtn-money', 'wave'])) {
            $numeros = [
                'orange-money' => '07 48 11 39 65',
                'mtn-money' => '05 55 95 06 08', 
                'wave' => '05 06 06 45 95'
            ];
            $paiement_info .= "<p><strong>Numéro de paiement :</strong> " . $numeros[$moyen_paiement] . "</p>";
        }
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            $emailCSS
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🐓 Commande Confirmée</h1>
                    <p>Poulets  des Fêtes 2025 - AGRIFORLAND RANCH</p>
                </div>
                
                <div class='content'>
                    <div class='greeting'>
                        Bonjour <strong>$nom</strong>,
                    </div>
                    
                    <p>Nous avons bien reçu votre commande de coqs et coquelets pour les fêtes 2025. Merci de nous faire confiance !</p>
                    
                    <div class='section'>
                        <h3>📋 Détails de votre commande</h3>
                        <table class='order-table'>
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th style='text-align:center'>Quantité</th>
                                    <th style='text-align:right'>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                $commande_details
                                $calculs_details
                                <tr class='total-row'>
                                    <td colspan='2'><strong>TOTAL À PAYER</strong></td>
                                    <td style='text-align:right; font-size:18px'><strong>" . number_format($total_final, 0, ',', ' ') . " FCFA</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class='section'>
                        <h3>🚚 Informations de livraison</h3>
                        $livraison_info
                    </div>
                    
                    <div class='section'>
                        <h3>💰 Paiement</h3>
                        $paiement_info
                    </div>
                    
                    <div class='info-box'>
                        <h4>📞 Prochaines étapes</h4>
                        <ul>
                            <li>Notre équipe vous contactera dans les <strong>2 heures</strong></li>
                            <li>Confirmation des détails et instructions de paiement</li>
                            <li>Préparation et livraison selon vos préférences</li>
                        </ul>
                    </div>
                    
                    <div class='contact-info'>
                        <h4>Contact direct</h4>
                        <div class='contact-item'>📞 +225 27 22 332 336</div>
                        <div class='contact-item'>📱 WhatsApp: +225 27 22 332 336</div>
                        <div class='contact-item'>✉️ ranch@agriforland.com</div>
                    </div>
                    
                    <p>Nous nous réjouissons de vous servir et vous souhaitons d'excellentes fêtes de fin d'année !</p>
                    
                    <p style='margin-top: 30px;'><strong>Cordialement,<br>L'équipe AGRIFORLAND RANCH</strong></p>
                </div>
                
                <div class='footer'>
                    <p>© 2025 AGRIFORLAND RANCH </p>
                    <p>Côte d'Ivoire - Abidjan</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Envoyer au client
        $mail->send();
        
        // EMAIL INTERNE (équipe)
        $mail->clearAddresses();
        $mail->addAddress(CONTACT_EMAIL, 'Équipe AGRIFORLAND');
        $mail->Subject = '🔔 Nouvelle commande poulets - ' . $nom . ' (' . $total_qty . ' poulets)';
        
        // Informations client pour l'équipe
        $client_info = "
        <p><strong>Nom :</strong> $nom<br>
        <strong>Téléphone :</strong> $telephone<br>
        <strong>Email :</strong> $email</p>";
        
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            $emailCSS
        </head>
        <body>
            <div class='container'>
                <div class='header' style='background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);'>
                    <h1>🔔 Nouvelle Commande</h1>
                    <p>Poulets des Fêtes 2025</p>
                </div>
                
                <div class='content'>
                    <div class='info-box' style='background: #fff3cd; border-color: #ffeaa7;'>
                        <h4 style='color: #856404;'>⚡ Action requise</h4>
                        <p style='color: #856404; margin: 0;'><strong>Contacter le client dans les 2 heures</strong></p>
                    </div>
                    
                    <div class='section'>
                        <h3>👤 Informations client</h3>
                        $client_info
                    </div>
                    
                    <div class='section'>
                        <h3>📋 Commande détaillée</h3>
                        <table class='order-table'>
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th style='text-align:center'>Quantité</th>
                                    <th style='text-align:right'>Prix unitaire</th>
                                    <th style='text-align:right'>Montant</th>
                                </tr>
                            </thead>
                            <tbody>";
        
        if ($poulet_1kg > 0) $mail->Body .= "<tr><td>Poulets  (1-1,2kg)</td><td style='text-align:center'>$poulet_1kg</td><td style='text-align:right'>3 000 FCFA</td><td style='text-align:right'>" . number_format($poulet_1kg * 3000, 0, ',', ' ') . " FCFA</td></tr>";
        if ($poulet_13kg > 0) $mail->Body .= "<tr><td>Poulets  (1,3-1,5kg)</td><td style='text-align:center'>$poulet_13kg</td><td style='text-align:right'>4 500 FCFA</td><td style='text-align:right'>" . number_format($poulet_13kg * 4500, 0, ',', ' ') . " FCFA</td></tr>";
        if ($poulet_16kg > 0) $mail->Body .= "<tr><td>Poulets  (1,6-1,9kg)</td><td style='text-align:center'>$poulet_16kg</td><td style='text-align:right'>5 500 FCFA</td><td style='text-align:right'>" . number_format($poulet_16kg * 5500, 0, ',', ' ') . " FCFA</td></tr>";
        if ($poulet_2kg > 0) $mail->Body .= "<tr><td>Poulets  (2-2,5kg)</td><td style='text-align:center'>$poulet_2kg</td><td style='text-align:right'>7 500 FCFA</td><td style='text-align:right'>" . number_format($poulet_2kg * 7500, 0, ',', ' ') . " FCFA</td></tr>";
        if ($poulet_26kg > 0) $mail->Body .= "<tr><td>Poulets  (+2,6kg)</td><td style='text-align:center'>$poulet_26kg</td><td style='text-align:right'>8 500 FCFA</td><td style='text-align:right'>" . number_format($poulet_26kg * 8500, 0, ',', ' ') . " FCFA</td></tr>";
        
        $mail->Body .= "
                                <tr class='total-row'>
                                    <td colspan='3'><strong>TOTAL FINAL</strong></td>
                                    <td style='text-align:right; font-size:18px'><strong>" . number_format($total_final, 0, ',', ' ') . " FCFA</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class='section'>
                        <h3>🚚 Détails logistiques</h3>
                        $livraison_info
                    </div>
                    
                    <div class='section'>
                        <h3>💰 Paiement</h3>
                        <p><strong>Mode :</strong> $mode_paiement<br>
                        <strong>Moyen :</strong> " . ($moyen_paiement ?: 'Non spécifié') . "</p>
                        $paiement_info
                    </div>
                    
                    <div class='section'>
                        <h3>📝 Options & Instructions</h3>
                        <p><strong>Emballage :</strong> $emballage<br>
                        <strong>Préparation :</strong> $preparation</p>";
        
        if ($instructions) {
            $mail->Body .= "<p><strong>Instructions spéciales :</strong><br>" . nl2br(htmlspecialchars($instructions)) . "</p>";
        }
        
        $mail->Body .= "
                    </div>
                    
                    <div class='contact-info' style='background: #e8f4fd;'>
                        <h4 style='color: #2980b9;'>📞 Coordonnées client</h4>
                        <div style='color: #2980b9;'><strong>☎️ $telephone</strong></div>
                        <div style='color: #2980b9;'>✉️ $email</div>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>Système de commande AGRIFORLAND RANCH</p>
                    <p>Email automatique - Ne pas répondre</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Envoyer en interne
        $mail->send();
        
        $success = true;
        
    } catch (Exception $e) {
        // Sauvegarder les données en cas d'échec email
        $logData = "=== COMMANDE DU " . date('Y-m-d H:i:s') . " ===\n";
        $logData .= "Client: $nom ($telephone - $email)\n";
        $logData .= "Commande: ";
        if ($poulet_1kg > 0) $logData .= "$poulet_1kg x 1-1.2kg, ";
        if ($poulet_13kg > 0) $logData .= "$poulet_13kg x 1.3-1.5kg, ";
        if ($poulet_16kg > 0) $logData .= "$poulet_16kg x 1.6-1.9kg, ";
        if ($poulet_2kg > 0) $logData .= "$poulet_2kg x 2-2.5kg, ";
        if ($poulet_26kg > 0) $logData .= "$poulet_26kg x +2.6kg, ";
        $logData .= "\nTotal: " . number_format($total_final, 0, ',', ' ') . " FCFA\n";
        $logData .= "Livraison: $mode_livraison ($commune)\n";
        $logData .= "Date: $date_livraison\n";
        $logData .= "Paiement: $mode_paiement ($moyen_paiement)\n";
        $logData .= "Erreur SMTP: " . $mail->ErrorInfo . "\n";
        $logData .= "=====================================\n\n";
        
        // Sauvegarder dans un fichier log
        file_put_contents('../logs/commandes_failed.txt', $logData, FILE_APPEND | LOCK_EX);
        
        $error = "Erreur lors de l'envoi : " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdn.tailwindcss.com">
  <link rel="preconnect" href="https://unpkg.com">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="description" content="Commandez vos coqs et coquelets des fêtes 2025 chez AGRIFORLAND RANCH. Poulets de différents poids. Livraison et expedition partout.">
  <meta name="author" content="AGRIFORLAND SARL">
  <meta property="og:title" content="Commande Poulets  des Fêtes 2025 - AGRIFORLAND RANCH">
  <meta property="og:description" content="coqs et coquelets pour vos fêtes 2025. Commande en ligne, livraison à domicile. -10% pour les premiers clients !">
  <meta property="og:image" content="https://www.agriforland.com/cache/logo-198x66-1200.webp">
  <meta property="og:url" content="https://www.agriforland.com/commande-poulets.php">
  <meta name="twitter:card" content="summary_large_image">
  <link rel="canonical" href="https://www.agriforland.com/commande-poulets.php">
  <title data-i18n="page_title">Commande Poulets des Fêtes 2025 - AGRIFORLAND RANCH</title>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link href="../images/triangle-svgrepo-com.svg" rel="preload" as="image">
  <link href="../css/Style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/bold/style.css">
  <link rel="icon" href="../images/favicon.ico" type="image/x-icon">

  <style>
    .scrollbar-hide {
      scrollbar-width: none;
      -ms-overflow-style: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
      display: none;
    }

    .animate-triangle {
      animation: spin 2s linear infinite;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Menu mobile */
    #mobile-menu {
      transform: translateX(100%);
      transition: transform 0.3s ease-in-out;
    }
    #mobile-menu.open {
      transform: translateX(0);
    }

    /* Formulaire styles */
    .product-card {
      transition: all 0.3s ease;
      border: 2px solid #e5e7eb;
    }
    
    .product-card:hover {
      border-color: #a9cf46;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(169, 207, 70, 0.15);
    }
    
    .product-card.has-quantity {
      border-color: #759916;
      background: #f6ffde;
    }

    .quantity-control {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .quantity-btn {
      background: #a9cf46;
      color: white;
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      font-size: 1.2rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .quantity-btn:hover {
      background: #759916;
      transform: scale(1.1);
    }

    .quantity-input {
      width: 100px;
      text-align: center;
      font-weight: bold;
      font-size: 1.1rem;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      padding: 8px;
    }

    .quantity-input:focus {
      border-color: #a9cf46;
      outline: none;
    }

    .summary-card {
      background: linear-gradient(135deg, #2c541d 0%, #759916 100%);
      position: sticky;
      top: 20px;
    }

    .form-input, .form-select {
      transition: all 0.3s ease;
      border: 2px solid #e5e7eb;
    }

    .form-input:focus, .form-select:focus {
      border-color: #a9cf46;
      outline: none;
      box-shadow: 0 0 0 3px rgba(169, 207, 70, 0.1);
    }

    .payment-option {
      border: 2px solid #e5e7eb;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .payment-option:hover {
      border-color: #a9cf46;
    }

    .payment-option.selected {
      border-color: #759916;
      background: #f6ffde;
    }

    /* Étapes */
    .step-indicator {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 2rem;
    }

    .step {
      display: flex;
      align-items: center;
      padding: 0.5rem 1rem;
      border-radius: 2rem;
      margin: 0 0.5rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .step.active {
      background: #a9cf46;
      color: white;
      font-weight: bold;
    }

    .step.completed {
      background: #27ae60;
      color: white;
    }

    .step.inactive {
      background: #e5e7eb;
      color: #6b7280;
    }

    .step-content {
      display: none;
    }

    .step-content.active {
      display: block;
    }

    /* Responsive optimizations */
    @media (max-width: 768px) {
      .quantity-control {
        justify-content: center;
      }
      
      .mobile-stack {
        grid-template-columns: 1fr !important;
      }

      .step {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
      }
    }
  </style>

  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Product",
      "name": "Poulets des Fêtes  - AGRIFORLAND RANCH",
      "description": " Coqs et coquelets pour les fêtes : différents poids disponibles",
      "brand": {
        "@type": "Brand",
        "name": "AGRIFORLAND RANCH"
      },
      "offers": {
        "@type": "AggregateOffer",
        "priceCurrency": "XOF",
        "lowPrice": "3000",
        "highPrice": "8500"
      }
    }
  </script>
</head>

<body class="bg-[#f6ffde] text-black">
  <!-- Preloader -->
  <div id="preloader" class="fixed inset-0 bg-[#f6ffde] z-50 flex items-center justify-center">
    <div class="animate-triangle w-20 h-20 md:w-24 md:h-24">
      <img src="../images/triangle-svgrepo-com.svg" loading="lazy" alt="Chargement..." class="w-full h-full object-contain triangle-img">
    </div>
  </div>

  <!-- Header identique au site -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-3 md:px-4 py-2 md:py-3 flex items-center justify-between">
      <img 
        src="../cache/logo-198x66-800.webp" 
        srcset="
          ../cache/logo-198x66-480.webp 480w, 
          ../cache/logo-198x66-800.webp 800w, 
          ../cache/logo-198x66-1200.webp 1200w
        "
        sizes="(max-width: 600px) 120px, (max-width: 1000px) 160px, 200px"
        loading="lazy" 
        alt="AGRIFORLAND - Logo cabinet conseil multidisciplinaire" 
        class="h-8 sm:h-10"
      />    
      
      <!-- Menu Burger pour mobile -->
      <button id="menu-toggle" class="md:hidden text-gray-700 focus:outline-none p-2" aria-label="Ouvrir le menu">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
              viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
      
      <!-- Boutons (desktop) -->
      <div class="hidden md:flex gap-3 items-center ml-auto">
        <!-- Language Selector -->
        <div class="relative inline-block text-left">
          <select id="language-selector" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-2 py-1 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline text-sm">
            <option value="fr" data-icon="../images/fr.webp">Français</option>
            <option value="en" data-icon="../images/en.webp">English</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
            <img id="language-icon" loading="lazy" src="../images/fr.webp" alt="Language" class="h-4 w-4">
          </div>
        </div>
        <a href="../recrutement.html" class="bg-[#759916] text-white px-3 py-2 rounded-md hover:text-black hover:bg-[#ade126] transition text-sm whitespace-nowrap" data-i18n="join_us">
          Nous Rejoindre
        </a>
        <a href="../contact.html" class="border border-gray-500 px-3 py-2 rounded-md hover:text-black hover:bg-[#f6ffde] transition text-sm whitespace-nowrap" data-i18n="contact_us">
          Nous Contacter
        </a>
      </div>
    </div>

    <!-- Navigation Desktop -->
    <div class="border-t border-gray-100 bg-[#f6ffde] hidden md:block">
      <nav class="max-w-7xl mx-auto px-4 py-3 flex justify-center gap-6 lg:gap-8 text-base lg:text-lg">
        <a href="../index.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="home">Accueil</a>
        <a href="../about.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="about">À Propos</a>
        <a href="../poles.html" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="poles">Nos Pôles</a>
        <a href="../projets.html" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="projects">Nos Projets</a>
        <a href="../blog.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="blog">Blog</a>
        <a href="../portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="portfolios">Portfolio</a>
      </nav>
    </div>

    <!-- Menu Mobile -->
    <div id="mobile-menu" class="md:hidden fixed top-0 right-0 h-full w-3/4 bg-[#f6ffde] px-4 pb-4 z-50 hidden shadow-xl">
      <div class="flex justify-end pt-4 pb-2">
        <button id="menu-close" class="text-gray-700 p-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      
      <nav class="flex flex-col gap-3 text-base mb-6">
        <a href="../index.php" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="home">Accueil</a>
        <a href="../about.php" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="about">À Propos</a>
        <a href="../poles.html" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="poles">Nos Pôles</a>
        <a href="../projets.html" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="projects">Nos Projets</a>
        <a href="../blog.php" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="blog">Blog</a>
        <a href="../portfolios.php" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="portfolios">Portfolio</a>
      </nav>
      
      <div class="flex flex-col gap-3">
        <div class="relative">
          <select id="language-selector-mobile" class="block appearance-none bg-white border border-gray-300 px-2 py-2 pr-8 rounded shadow w-full text-sm">
            <option value="fr" data-icon="../images/fr.webp">Français</option>
            <option value="en" data-icon="../images/en.webp">English</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
            <img id="language-icon-mobile" loading="lazy" src="../images/fr.webp" alt="Language" class="h-4 w-4">
          </div>
        </div>
        <a href="../recrutement.html" class="bg-[#759916] text-white px-3 py-2 rounded-md text-center text-sm hover:bg-[#ade126] transition">Nous Rejoindre</a>
        <a href="../contact.html" class="border border-gray-500 px-3 py-2 rounded-md text-center text-sm hover:bg-white transition">Nous contacter</a>
      </div>
    </div>
  </header>

  <?php if (isset($success) && $success): ?>
  <!-- Message de succès -->
  <section class="py-12 px-4">
    <div class="max-w-4xl mx-auto">
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-[#27ae60] to-[#2ecc71] text-white p-8 text-center">
          <i class="ph ph-check-circle text-6xl mb-4 block"></i>
          <h1 class="text-3xl font-bold font-kanit mb-2" data-i18n="order_success_title">Commande enregistrée avec succès !</h1>
          <p class="text-xl opacity-90" data-i18n="order_success_subtitle">Merci pour votre confiance</p>
        </div>
        
        <div class="p-8">
          <div class="bg-[#e8f4fd] rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-[#2980b9] mb-3">Prochaines étapes</h2>
            <div class="space-y-3">
              <div class="flex items-center gap-3">
                <i class="ph ph-phone text-[#2980b9]"></i>
                <span>Notre équipe vous contactera dans les <strong>2 heures</strong></span>
              </div>
              <div class="flex items-center gap-3">
                <i class="ph ph-credit-card text-[#2980b9]"></i>
                <span>Confirmation des détails et instructions de paiement</span>
              </div>
              <div class="flex items-center gap-3">
                <i class="ph ph-truck text-[#2980b9]"></i>
                <span>Préparation et livraison selon vos préférences</span>
              </div>
            </div>
          </div>
          
          <div class="bg-[#f8f9fa] rounded-lg p-6 text-center">
            <h3 class="font-semibold text-gray-800 mb-3">Contact direct</h3>
            <div class="space-y-2 text-gray-600">
              <p><i class="ph ph-phone mr-2"></i> +225 27 22 332 336</p>
              <p><i class="ph ph-whatsapp-logo mr-2"></i> WhatsApp: +225 27 22 332 336</p>
              <p><i class="ph ph-envelope mr-2"></i> ranch@agriforland.com</p>
            </div>
          </div>
          
          <div class="text-center mt-8">
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="bg-[#a9cf46] text-black px-8 py-3 rounded-lg font-semibold hover:bg-[#759916] hover:text-white transition-colors">
              Nouvelle commande
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
  
  <?php elseif (isset($error)): ?>
  <!-- Message d'erreur -->
  <section class="py-12 px-4">
    <div class="max-w-2xl mx-auto">
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-[#e74c3c] text-white p-8 text-center">
          <i class="ph ph-warning text-6xl mb-4 block"></i>
          <h1 class="text-2xl font-bold">Erreur lors de l'envoi</h1>
        </div>
        <div class="p-8 text-center">
          <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($error); ?></p>
          <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="bg-[#a9cf46] text-black px-6 py-3 rounded-lg hover:bg-[#759916] hover:text-white transition-colors">
            Retour au formulaire
          </a>
        </div>
      </div>
    </div>
  </section>
  
  <?php else: ?>
  
  <!-- Hero section -->
  <section class="bg-gradient-to-r from-[#a9cf46] to-[#759916] text-white py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h1 class="text-4xl md:text-5xl font-bold font-kanit mb-4" data-i18n="hero_title">
        Poulets des Fêtes 2025
      </h1>
      <p class="text-xl md:text-2xl mb-6 opacity-90" data-i18n="hero_subtitle">
        AGRIFORLAND RANCH 
      </p>
      <div class="inline-block bg-[#e74c3c] text-white px-6 py-3 rounded-full font-bold text-lg animate-pulse">
        <i class="ph ph-fire mr-2"></i>
        <span data-i18n="promo_badge">-10% pour les 5 premiers clients !</span>
      </div>
    </div>
  </section>

  <!-- Formulaire principal -->
  <section class="py-12 px-4">
    <form id="orderForm" method="POST" class="max-w-7xl mx-auto">
      <input type="hidden" name="action" value="submit_order">
      
      <!-- Indicateur d'étapes -->
      <div class="step-indicator">
        <div class="step active" data-step="1">
          <i class="ph ph-shopping-cart mr-2"></i>
          <span>1. Produits</span>
        </div>
        <div class="step inactive" data-step="2">
          <i class="ph ph-user mr-2"></i>
          <span>2. Informations</span>
        </div>
        <div class="step inactive" data-step="3">
          <i class="ph ph-credit-card mr-2"></i>
          <span>3. Paiement</span>
        </div>
      </div>
      
      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-8">
          
          <!-- Étape 1: Sélection des produits -->
          <div id="step-1" class="step-content active bg-white rounded-xl shadow-lg p-6 md:p-8">
            <h2 class="text-2xl font-bold font-kanit text-[#2c541d] mb-6 flex items-center">
              <i class="ph ph-shopping-cart mr-3"></i>
              <span data-i18n="step1_title">Choisissez vos coqs et coquelets</span>
            </h2>
            
            <div class="space-y-6">
              <!-- Poulet 1kg-1.2kg -->
              <div class="product-card bg-[#fbfff0] rounded-lg p-6" data-product="poulet_1kg">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <span class="text-2xl">🐓</span>
                      <h3 class="text-xl font-semibold text-[#2c541d]">Standard  (1kg - 1,2kg)</h3>
                    </div>
                    <p class="text-gray-600 mb-2">Idéal pour une petite famille ou un repas rapide. Convient aux plats mijotés ou grillés.</p>
                    <div class="text-2xl font-bold text-[#e74c3c]">3 000 FCFA</div>
                  </div>
                  <div class="quantity-control">
                    <button type="button" class="quantity-btn" onclick="updateQuantity('poulet_1kg', -1)">
                      <i class="ph ph-minus"></i>
                    </button>
                    <input type="number" class="quantity-input" name="qty_poulet_1kg" id="qty_poulet_1kg" value="0" min="0" max="999">
                    <button type="button" class="quantity-btn" onclick="updateQuantity('poulet_1kg', 1)">
                      <i class="ph ph-plus"></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Poulet 1.3kg-1.5kg -->
              <div class="product-card bg-[#fbfff0] rounded-lg p-6" data-product="poulet_13kg">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <span class="text-2xl">🐓</span>
                      <h3 class="text-xl font-semibold text-[#2c541d]">Big  (1,3kg - 1,5kg)</h3>
                    </div>
                    <p class="text-gray-600 mb-2">Format économique, parfait pour 3 à 4 personnes. Bon équilibre entre prix et quantité.</p>
                    <div class="text-2xl font-bold text-[#e74c3c]">4 500 FCFA</div>
                  </div>
                  <div class="quantity-control">
                    <button type="button" class="quantity-btn" onclick="updateQuantity('poulet_13kg', -1)">
                      <i class="ph ph-minus"></i>
                    </button>
                    <input type="number" class="quantity-input" name="qty_poulet_13kg" id="qty_poulet_13kg" value="0" min="0" max="999">
                    <button type="button" class="quantity-btn" onclick="updateQuantity('poulet_13kg', 1)">
                      <i class="ph ph-plus"></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Poulet 1.6kg-1.9kg -->
              <div class="product-card bg-[#fbfff0] rounded-lg p-6" data-product="poulet_16kg">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <span class="text-2xl">🐓</span>
                      <h3 class="text-xl font-semibold text-[#2c541d]">Super (1,6kg - 1,9kg)</h3>
                    </div>
                    <p class="text-gray-600 mb-2">Le plus populaire ⭐. Convient aux familles nombreuses, repas du dimanche ou grillades.</p>
                    <div class="text-2xl font-bold text-[#e74c3c]">5 500 FCFA</div>
                  </div>
                  <div class="quantity-control">
                    <button type="button" class="quantity-btn" onclick="updateQuantity('poulet_16kg', -1)">
                      <i class="ph ph-minus"></i>
                    </button>
                    <input type="number" class="quantity-input" name="qty_poulet_16kg" id="qty_poulet_16kg" value="0" min="0" max="999">
                    <button type="button" class="quantity-btn" onclick="updateQuantity('poulet_16kg', 1)">
                      <i class="ph ph-plus"></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Poulet 2kg-2.5kg -->
              <div class="product-card bg-[#fbfff0] rounded-lg p-6" data-product="poulet_2kg">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <span class="text-2xl">🐓</span>
                      <h3 class="text-xl font-semibold text-[#2c541d]">Master  (2kg - 2,5kg)</h3>
                    </div>
                    <p class="text-gray-600 mb-2">Gros format, idéal pour les occasions spéciales, barbecues ou plats festifs.</p>
                    <div class="text-2xl font-bold text-[#e74c3c]">7 500 FCFA</div>
                  </div>
                  <div class="quantity-control">
                    <button type="button" class="quantity-btn" onclick="updateQuantity('poulet_2kg', -1)">
                      <i class="ph ph-minus"></i>
                    </button>
                    <input type="number" class="quantity-input" name="qty_poulet_2kg" id="qty_poulet_2kg" value="0" min="0" max="999">
                    <button type="button" class="quantity-btn" onclick="updateQuantity('poulet_2kg', 1)">
                      <i class="ph ph-plus"></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Poulet +2.6kg -->
              <div class="product-card bg-[#fbfff0] rounded-lg p-6" data-product="poulet_26kg">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <span class="text-2xl">🐓</span>
                      <h3 class="text-xl font-semibold text-[#2c541d]">Géant  (+2,6kg)</h3>
                    </div>
                    <p class="text-gray-600 mb-2">Le maximum de chair ! Adapté pour les grands rassemblements, restaurants et événements.</p>
                    <div class="text-2xl font-bold text-[#e74c3c]">8 500 FCFA</div>
                  </div>
                  <div class="quantity-control">
                    <button type="button" class="quantity-btn" onclick="updateQuantity('poulet_26kg', -1)">
                      <i class="ph ph-minus"></i>
                    </button>
                    <input type="number" class="quantity-input" name="qty_poulet_26kg" id="qty_poulet_26kg" value="0" min="0" max="999">
                    <button type="button" class="quantity-btn" onclick="updateQuantity('poulet_26kg', 1)">
                      <i class="ph ph-plus"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div id="promo-info" class="bg-[#fff3cd] border border-[#ffeaa7] rounded-lg p-4 mt-6">
              <div class="flex items-start gap-3">
                <i class="ph ph-info text-[#856404] text-xl mt-0.5"></i>
                <div>
                  <h4 class="font-semibold text-[#856404] mb-1">Offre spéciale limitée</h4>
                  <p class="text-[#856404] text-sm">
                    <strong>-10% de remise</strong> automatique pour les commandes de <strong>5 poulets ou plus</strong>.
                    Offre valable pour les 5 premiers clients seulement !
                  </p>
                </div>
              </div>
            </div>

            <div class="flex justify-end mt-8">
              <button type="button" onclick="nextStep(2)" id="next-step-1" class="bg-[#a9cf46] text-black px-8 py-3 rounded-lg font-semibold hover:bg-[#759916] hover:text-white transition-colors" disabled>
                Continuer <i class="ph ph-arrow-right ml-2"></i>
              </button>
            </div>
          </div>

          <!-- Étape 2: Informations client -->
          <div id="step-2" class="step-content bg-white rounded-xl shadow-lg p-6 md:p-8">
            <h2 class="text-2xl font-bold font-kanit text-[#2c541d] mb-6 flex items-center">
              <i class="ph ph-user mr-3"></i>
              <span data-i18n="step2_title">Vos informations</span>
            </h2>
            
            <div class="grid md:grid-cols-2 gap-6 mobile-stack">
              <div>
                <label for="nom" class="block text-sm font-semibold text-gray-700 mb-2">
                  Nom complet <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nom" name="nom" required 
                       class="form-input w-full px-4 py-3 rounded-lg" 
                       placeholder="Ex: KOUADIO Jean-Baptiste">
              </div>
              
              <div>
                <label for="telephone" class="block text-sm font-semibold text-gray-700 mb-2">
                  Téléphone <span class="text-red-500">*</span>
                </label>
                <input type="tel" id="telephone" name="telephone" required 
                       class="form-input w-full px-4 py-3 rounded-lg" 
                       placeholder="07 00 00 00 00">
              </div>
              
              <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                  Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email" required 
                       class="form-input w-full px-4 py-3 rounded-lg" 
                       placeholder="votre.email@exemple.com">
              </div>
              
              <div>
                <label for="mode_livraison" class="block text-sm font-semibold text-gray-700 mb-2">
                  Mode de récupération <span class="text-red-500">*</span>
                </label>
                <select id="mode_livraison" name="mode_livraison" required 
                        class="form-select w-full px-4 py-3 rounded-lg" 
                        onchange="toggleDeliveryOptions()">
                  <option value="">Choisissez...</option>
                  <option value="livraison">Livraison à domicile</option>
                  <option value="expedition">Expédition (3 000 FCFA)</option>
                  <option value="retrait">Retrait à la ferme</option>
                </select>
              </div>
            </div>
            
            <div id="delivery-options" class="mt-6" style="display: none;">
              <div class="grid md:grid-cols-2 gap-6 mobile-stack">
                <div>
                  <label for="commune" class="block text-sm font-semibold text-gray-700 mb-2">
                    Commune/Zone <span class="text-red-500">*</span>
                  </label>
                  <select id="commune" name="commune" 
                          class="form-select w-full px-4 py-3 rounded-lg" 
                          onchange="updateSummary()">
                    <option value="">Sélectionnez votre zone</option>
                    <option value="cocody">Cocody (1 500 FCFA)</option>
                    <option value="dimbokro">Dimbokro (500 FCFA)</option>
                    <option value="autres">Autres communes (2 000 FCFA)</option>
                  </select>
                </div>
              </div>
              
              <div class="mt-4">
                <label for="adresse" class="block text-sm font-semibold text-gray-700 mb-2">
                  Adresse complète de livraison <span class="text-red-500">*</span>
                </label>
                <textarea id="adresse" name="adresse" rows="2" 
                          class="form-input w-full px-4 py-3 rounded-lg" 
                          placeholder="Rue, quartier, points de repère..."></textarea>
              </div>
            </div>

            <div id="expedition-options" class="mt-6" style="display: none;">
              <div>
                <label for="adresse_expedition" class="block text-sm font-semibold text-gray-700 mb-2">
                  Adresse d'expédition complète <span class="text-red-500">*</span>
                </label>
                <textarea id="adresse_expedition" name="adresse" rows="3" 
                          class="form-input w-full px-4 py-3 rounded-lg" 
                          placeholder="Ville, quartier, rue, points de repère, numéro de téléphone du destinataire..."></textarea>
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mobile-stack mt-6">
              <div>
                <label for="preparation" class="block text-sm font-semibold text-gray-700 mb-2">
                  Préparation <span class="text-red-500">*</span>
                </label>
                <select id="preparation" name="preparation" required 
                        class="form-select w-full px-4 py-3 rounded-lg">
                  <option value="">Choisissez...</option>
                  <option value="vif">Poulet vivant</option>
                  <option value="abattu">Poulet abattu (prêt à cuire)</option>
                  <option value="abattu-halal">Poulet abattu halal certifié</option>
                  <option value="abattu-fume">Poulet fumé</option>

                </select>
              </div>
              
              <div>
                <label for="date_livraison" class="block text-sm font-semibold text-gray-700 mb-2">
                  Date souhaitée <span class="text-red-500">*</span>
                </label>
                <input type="date" id="date_livraison" name="date_livraison" required 
                       class="form-input w-full px-4 py-3 rounded-lg">
              </div>
            </div>

            <!-- Options avancées -->
            <details class="mt-6">
              <summary class="cursor-pointer font-semibold text-[#759916] hover:text-[#a9cf46] transition-colors">
                <i class="ph ph-gear mr-2"></i>Options avancées
              </summary>
              <div class="mt-4 space-y-4 border-t pt-4">
                <div class="grid md:grid-cols-2 gap-6">
                  <div>
                    <label for="heure_livraison" class="block text-sm font-semibold text-gray-700 mb-2">
                      Créneau horaire
                    </label>
                    <select id="heure_livraison" name="heure_livraison" 
                            class="form-select w-full px-4 py-3 rounded-lg">
                      <option value="">Choisissez...</option>
                      <option value="08h-10h">08h - 10h</option>
                      <option value="10h-12h">10h - 12h</option>
                      <option value="12h-14h">12h - 14h</option>
                      <option value="14h-16h">14h - 16h</option>
                      <option value="16h-18h">16h - 18h</option>
                    </select>
                  </div>

                  <div>
                    <label for="emballage" class="block text-sm font-semibold text-gray-700 mb-2">
                      Type d'emballage
                    </label>
                    <select id="emballage" name="emballage" 
                            class="form-select w-full px-4 py-3 rounded-lg" 
                            onchange="updateSummary()">
                      <option value="standard">Emballage standard (gratuit)</option>
                      <option value="premium">Emballage premium (+500 FCFA/poulet)</option>
                      <option value="cadeau">Emballage cadeau (+1 000 FCFA/poulet)</option>
                    </select>
                  </div>
                </div>
                
                <div>
                  <label for="instructions" class="block text-sm font-semibold text-gray-700 mb-2">
                    Instructions spéciales
                  </label>
                  <textarea id="instructions" name="instructions" rows="3" 
                            class="form-input w-full px-4 py-3 rounded-lg" 
                            placeholder="Découpes particulières, conditionnement spécial..."></textarea>
                </div>
              </div>
            </details>

            <div class="flex justify-between mt-8">
              <button type="button" onclick="previousStep(1)" class="bg-gray-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                <i class="ph ph-arrow-left mr-2"></i> Retour
              </button>
              <button type="button" onclick="nextStep(3)" id="next-step-2" class="bg-[#a9cf46] text-black px-8 py-3 rounded-lg font-semibold hover:bg-[#759916] hover:text-white transition-colors">
                Continuer <i class="ph ph-arrow-right ml-2"></i>
              </button>
            </div>
          </div>

          <!-- Étape 3: Paiement -->
          <div id="step-3" class="step-content bg-white rounded-xl shadow-lg p-6 md:p-8">
            <h2 class="text-2xl font-bold font-kanit text-[#2c541d] mb-6 flex items-center">
              <i class="ph ph-credit-card mr-3"></i>
              <span data-i18n="step3_title">Mode de paiement</span>
            </h2>
            
            <div class="grid md:grid-cols-3 gap-4 mb-6">
              <div class="payment-option rounded-lg p-4 text-center" onclick="selectPaymentMode('acompte')">
                <i class="ph ph-coins text-3xl text-[#a9cf46] mb-2 block"></i>
                <h4 class="font-semibold mb-1">Acompte 50%</h4>
                <p class="text-sm text-gray-600">Payez la moitié maintenant</p>
              </div>
              
              <div class="payment-option rounded-lg p-4 text-center" onclick="selectPaymentMode('integral')">
                <i class="ph ph-check-circle text-3xl text-[#27ae60] mb-2 block"></i>
                <h4 class="font-semibold mb-1">Paiement intégral</h4>
                <p class="text-sm text-gray-600"><strong>-2% de remise</strong></p>
              </div>
              
              <div class="payment-option rounded-lg p-4 text-center" onclick="selectPaymentMode('livraison')">
                <i class="ph ph-truck text-3xl text-[#e74c3c] mb-2 block"></i>
                <h4 class="font-semibold mb-1">À la livraison</h4>
                <p class="text-sm text-gray-600">Paiement cash</p>
              </div>
            </div>
            
            <input type="hidden" id="mode_paiement" name="mode_paiement">
            
            <div>
              <label for="moyen_paiement" class="block text-sm font-semibold text-gray-700 mb-2">
                Moyen de paiement préféré
              </label>
              <select id="moyen_paiement" name="moyen_paiement" 
                      class="form-select w-full px-4 py-3 rounded-lg max-w-sm">
                <option value="">Choisissez...</option>
                <option value="especes">Espèces</option>
                <option value="orange-money">Orange Money</option>
                <option value="mtn-money">MTN Money</option>
                <option value="wave">Wave</option>
              </select>
            </div>

            <!-- Conditions -->
            <div class="bg-[#fff3cd] rounded-lg p-4 mt-6">
              <h4 class="font-semibold text-[#856404] mb-3">
                <i class="ph ph-list-checks mr-2"></i>Conditions de vente
              </h4>
              <ul class="text-[#856404] text-sm space-y-1 ml-4 list-disc">
                <li>Animaux abattus le jour de la livraison pour fraîcheur maximale</li>
                <li>Possibilité d'annulation jusqu'à 48h avant la date prévue</li>
                <li>Garantie fraîcheur ou remboursement intégral</li>
                <li>Livraison gratuite pour commandes > 50 000 FCFA</li>
                <li>Remise de 2% pour paiement intégral</li>
              </ul>
              
              <label class="flex items-start gap-3 mt-4 cursor-pointer">
                <input type="checkbox" id="accept_terms" name="accept_terms" required 
                       class="mt-1 transform scale-125">
                <span class="text-sm text-[#856404]">
                  J'accepte les conditions de vente <span class="text-red-500">*</span>
                </span>
              </label>
            </div>

            <div class="flex justify-between mt-8">
              <button type="button" onclick="previousStep(2)" class="bg-gray-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                <i class="ph ph-arrow-left mr-2"></i> Retour
              </button>
              <button type="submit" id="submitBtn" 
                      class="bg-gradient-to-r from-[#a9cf46] to-[#759916] text-white px-8 py-4 rounded-lg font-bold text-lg hover:from-[#759916] hover:to-[#2c541d] transition-all transform hover:scale-105 shadow-lg">
                <i class="ph ph-check-circle mr-2"></i>
                <span data-i18n="submit_order">Confirmer ma commande</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Colonne résumé (sticky) -->
        <div class="lg:col-span-1">
          <div class="summary-card text-white rounded-xl p-6 shadow-lg">
            <h3 class="text-xl font-bold font-kanit mb-4 flex items-center">
              <i class="ph ph-calculator mr-2"></i>
              <span data-i18n="order_summary">Résumé de commande</span>
            </h3>
            
            <div id="summary-content">
              <div class="text-center py-8 opacity-75">
                <i class="ph ph-shopping-cart text-4xl mb-2 block"></i>
                <p>Ajoutez des produits pour voir le résumé</p>
              </div>
            </div>
            
            <div class="mt-6 p-4 bg-white bg-opacity-10 rounded-lg">
              <h4 class="font-semibold mb-2">Contact direct</h4>
              <div class="space-y-1 text-sm">
                <p><i class="ph ph-phone mr-2"></i>+225 27 22 332 336</p>
                <p><i class="ph ph-envelope mr-2"></i>ranch@agriforland.com</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </section>

  <?php endif; ?>

  <!-- Footer -->
  <footer class="bg-[#3a3a3a] text-white py-12">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex flex-col md:flex-row justify-between items-center border-b border-white/20 pb-6">
        <div class="mb-4 md:mb-0">
          <img 
            src="../logo-inverse-198x66-800.webp" 
            srcset="
              ..//logo-inverse-198x66-480.webp 480w, 
              ../cache/logo-inverse-198x66-800.webp 800w, 
              ../cache/logo-inverse-198x66-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="AGRIFORLAND - Logo inverse cabinet conseil multidisciplinaire" 
            class="h-12"
          />
        </div>
        <div class="text-center md:text-right">
          <p class="font-bold mb-2">SUIVEZ-NOUS</p>
          <div class="flex justify-center md:justify-end gap-4 text-2xl">
            <a class="hover:text-[#a9cf46] transition-colors" href="#" aria-label="Facebook">
              <i class="ph ph-facebook-logo"></i>
            </a>
            <a class="hover:text-[#a9cf46] transition-colors" href="#" aria-label="Instagram">
              <i class="ph ph-instagram-logo"></i>
            </a>
            <a class="hover:text-[#a9cf46] transition-colors" href="#" aria-label="Twitter">
              <i class="ph ph-twitter-logo"></i>
            </a>
            <a class="hover:text-[#a9cf46] transition-colors" href="#" aria-label="LinkedIn">
              <i class="ph ph-linkedin-logo"></i>
            </a>
          </div>
        </div>
      </div>
      
      <div class="grid md:grid-cols-4 gap-8 mt-6">
        <div>
          <h4 class="font-bold mb-2">Liens Utiles</h4>
          <ul class="text-sm space-y-1">
            <li><a href="../about.php" class="hover:text-[#a9cf46] transition-colors">À Propos</a></li>
            <li><a href="../poles.html" class="hover:text-[#a9cf46] transition-colors">Nos Pôles</a></li>
            <li><a href="../projets.html" class="hover:text-[#a9cf46] transition-colors">Nos Projets</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-bold mb-2">Services</h4>
          <ul class="text-sm space-y-1">
            <li><a href="../blog.php" class="hover:text-[#a9cf46] transition-colors">Blog</a></li>
            <li><a href="../portfolios.php" class="hover:text-[#a9cf46] transition-colors">Portfolio</a></li>
            <li><a href="../contact.html" class="hover:text-[#a9cf46] transition-colors">Contact</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-bold mb-2">Ranch</h4>
          <ul class="text-sm space-y-1">
            <li><a href="../ranch.php" class="hover:text-[#a9cf46] transition-colors">Notre Ranch</a></li>
            <li><a href="../recrutement.html" class="hover:text-[#a9cf46] transition-colors">Nous Rejoindre</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-bold mb-2">Contact RANCH</h4>
          <div class="text-sm space-y-1">
            <p>+225 27 22 332 336</p>
            <p>ranch@agriforland.com</p>
          </div>
        </div>
      </div>
      
      <div class="flex flex-col md:flex-row justify-between items-center border-t border-white/20 py-6 mt-6 text-xs text-white/60">
        <div class="mb-2 md:mb-0">
          <a href="tel:+2252722332336" class="text-white font-bold hover:text-[#a9cf46] transition-colors">
            +225 27 22 332 336
          </a>
        </div>
        <div>© 2025 Agriforland. Tous droits réservés.</div>
      </div>
    </div>
  </footer>

  <script>
    // Configuration
    const prices = { 
      poulet_1kg: 3000, 
      poulet_13kg: 4500, 
      poulet_16kg: 5500, 
      poulet_2kg: 7500, 
      poulet_26kg: 8500 
    };
    
    let selectedPaymentMode = '';
    let currentStep = 1;

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
      const dateLivraison = document.getElementById('date_livraison');
      if (dateLivraison) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateLivraison.min = tomorrow.toISOString().split('T')[0];
      }
      
      // Event listeners pour les champs de quantité
      Object.keys(prices).forEach(product => {
        const input = document.getElementById(`qty_${product}`);
        if (input) {
          input.addEventListener('input', () => {
            updateSummary();
            updateStepButtons();
          });
          input.addEventListener('change', () => updateProductCard(product));
        }
      });
      
      updateSummary();
      updateStepButtons();
    });

    // Gestion des étapes
    function nextStep(step) {
      if (step === 2) {
        // Validation étape 1
        const totalQty = Object.keys(prices)
          .reduce((sum, product) => sum + parseInt(document.getElementById(`qty_${product}`).value || 0), 0);
        
        if (totalQty === 0) {
          alert('Veuillez sélectionner au moins un poulet avant de continuer.');
          return;
        }
      }
      
      if (step === 3) {
        // Validation étape 2
        const requiredFields = ['nom', 'telephone', 'email', 'mode_livraison', 'preparation', 'date_livraison'];
        for (const fieldId of requiredFields) {
          const field = document.getElementById(fieldId);
          if (!field.value.trim()) {
            field.focus();
            alert(`Le champ "${field.previousElementSibling.textContent.replace(' *', '')}" est requis.`);
            return;
          }
        }
        
        // Validation des champs conditionnels
        const modeLivraison = document.getElementById('mode_livraison').value;
        if (modeLivraison === 'livraison') {
          const commune = document.getElementById('commune').value;
          const adresse = document.getElementById('adresse').value;
          if (!commune) {
            document.getElementById('commune').focus();
            alert('Veuillez sélectionner votre commune.');
            return;
          }
          if (!adresse.trim()) {
            document.getElementById('adresse').focus();
            alert('Veuillez saisir votre adresse de livraison.');
            return;
          }
        } else if (modeLivraison === 'expedition') {
          const adresseExpedition = document.getElementById('adresse_expedition').value;
          if (!adresseExpedition.trim()) {
            document.getElementById('adresse_expedition').focus();
            alert('Veuillez saisir l\'adresse d\'expédition.');
            return;
          }
        }
      }
      
      // Changer d'étape
      document.getElementById(`step-${currentStep}`).classList.remove('active');
      document.querySelector(`[data-step="${currentStep}"]`).classList.remove('active');
      document.querySelector(`[data-step="${currentStep}"]`).classList.add('completed');
      
      currentStep = step;
      
      document.getElementById(`step-${currentStep}`).classList.add('active');
      document.querySelector(`[data-step="${currentStep}"]`).classList.remove('inactive');
      document.querySelector(`[data-step="${currentStep}"]`).classList.add('active');
      
      // Scroll vers le haut
      document.getElementById(`step-${currentStep}`).scrollIntoView({ behavior: 'smooth' });
    }

    function previousStep(step) {
      document.getElementById(`step-${currentStep}`).classList.remove('active');
      document.querySelector(`[data-step="${currentStep}"]`).classList.remove('active');
      document.querySelector(`[data-step="${currentStep}"]`).classList.add('inactive');
      
      currentStep = step;
      
      document.getElementById(`step-${currentStep}`).classList.add('active');
      document.querySelector(`[data-step="${currentStep}"]`).classList.remove('completed');
      document.querySelector(`[data-step="${currentStep}"]`).classList.add('active');
      
      // Scroll vers le haut
      document.getElementById(`step-${currentStep}`).scrollIntoView({ behavior: 'smooth' });
    }

    function updateStepButtons() {
      const totalQty = Object.keys(prices)
        .reduce((sum, product) => {
          const input = document.getElementById(`qty_${product}`);
          return sum + (input ? parseInt(input.value || 0) : 0);
        }, 0);
      
      const nextBtn = document.getElementById('next-step-1');
      if (nextBtn) {
        nextBtn.disabled = totalQty === 0;
        if (totalQty === 0) {
          nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
          nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
      }
    }

    function toggleDeliveryOptions() {
      const modeLivraison = document.getElementById('mode_livraison').value;
      const deliveryOptions = document.getElementById('delivery-options');
      const expeditionOptions = document.getElementById('expedition-options');
      
      if (modeLivraison === 'livraison') {
        deliveryOptions.style.display = 'block';
        expeditionOptions.style.display = 'none';
        document.getElementById('commune').required = true;
        document.getElementById('adresse').required = true;
        document.getElementById('adresse_expedition').required = false;
      } else if (modeLivraison === 'expedition') {
        deliveryOptions.style.display = 'none';
        expeditionOptions.style.display = 'block';
        document.getElementById('commune').required = false;
        document.getElementById('adresse').required = false;
        document.getElementById('adresse_expedition').required = true;
      } else {
        deliveryOptions.style.display = 'none';
        expeditionOptions.style.display = 'none';
        document.getElementById('commune').required = false;
        document.getElementById('adresse').required = false;
        document.getElementById('adresse_expedition').required = false;
      }
      
      updateSummary();
    }

    // Gestion menu mobile
    const menuToggle = document.getElementById('menu-toggle');
    const menuClose = document.getElementById('menu-close');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && mobileMenu) {
      menuToggle.addEventListener('click', () => {
        mobileMenu.classList.remove('hidden');
        setTimeout(() => mobileMenu.classList.add('open'), 10);
      });
    }

    if (menuClose && mobileMenu) {
      menuClose.addEventListener('click', () => {
        mobileMenu.classList.remove('open');
        setTimeout(() => mobileMenu.classList.add('hidden'), 300);
      });
    }

    // Gestion des quantités
    function updateQuantity(product, change) {
      const input = document.getElementById(`qty_${product}`);
      if (!input) return;
      
      const newValue = Math.max(0, parseInt(input.value || 0) + change);
      input.value = newValue;
      updateSummary();
      updateProductCard(product);
      updateStepButtons();
    }

    function updateProductCard(product) {
      const card = document.querySelector(`[data-product="${product}"]`);
      const input = document.getElementById(`qty_${product}`);
      
      if (!card || !input) return;
      
      const quantity = parseInt(input.value || 0);
      
      if (quantity > 0) {
        card.classList.add('has-quantity');
      } else {
        card.classList.remove('has-quantity');
      }
    }

    // Sélection mode de paiement
    function selectPaymentMode(mode) {
      document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('selected');
      });
      
      event.target.closest('.payment-option').classList.add('selected');
      selectedPaymentMode = mode;
      document.getElementById('mode_paiement').value = mode;
      updateSummary();
    }

    // Mise à jour du résumé
    function updateSummary() {
      const summaryContent = document.getElementById('summary-content');
      if (!summaryContent) return;

      let html = '';
      let subtotal = 0;
      let totalQty = 0;

      const productNames = {
        poulet_1kg: 'Poulets  (1-1,2kg)',
        poulet_13kg: 'Poulets  (1,3-1,5kg)', 
        poulet_16kg: 'Poulets  (1,6-1,9kg)',
        poulet_2kg: 'Poulets  (2-2,5kg)',
        poulet_26kg: 'Poulets  (+2,6kg)'
      };

      // Calcul produits
      for (const [product, price] of Object.entries(prices)) {
        const input = document.getElementById(`qty_${product}`);
        if (!input) continue;
        
        const qty = parseInt(input.value || 0);
        if (qty > 0) {
          const lineTotal = qty * price;
          subtotal += lineTotal;
          totalQty += qty;
          
          html += `<div class="flex justify-between items-center py-2 border-b border-white border-opacity-20">
            <span class="text-sm">${qty} × ${productNames[product]}</span>
            <span class="font-semibold">${lineTotal.toLocaleString()} FCFA</span>
          </div>`;
        }
      }

      if (subtotal === 0) {
        summaryContent.innerHTML = `
          <div class="text-center py-8 opacity-75">
            <i class="ph ph-shopping-cart text-4xl mb-2 block"></i>
            <p>Ajoutez des produits pour voir le résumé</p>
          </div>`;
        return;
      }

      html += `<div class="flex justify-between items-center py-2 text-lg font-semibold">
        <span>Sous-total</span>
        <span>${subtotal.toLocaleString()} FCFA</span>
      </div>`;

      // Remise -10%
      let discount = 0;
      if (totalQty >= 5) {
        discount = subtotal * 0.10;
        html += `<div class="flex justify-between items-center py-2 text-green-300">
          <span>🎉 Remise -10%</span>
          <span>-${discount.toLocaleString()} FCFA</span>
        </div>`;
      }

      // Emballage
      const emballage = document.getElementById('emballage')?.value || 'standard';
      let packagingPrice = 0;
      if (emballage === 'premium') {
        packagingPrice = totalQty * 500;
        html += `<div class="flex justify-between items-center py-2">
          <span>Emballage premium</span>
          <span>+${packagingPrice.toLocaleString()} FCFA</span>
        </div>`;
      } else if (emballage === 'cadeau') {
        packagingPrice = totalQty * 1000;
        html += `<div class="flex justify-between items-center py-2">
          <span>Emballage cadeau</span>
          <span>+${packagingPrice.toLocaleString()} FCFA</span>
        </div>`;
      }

      // Frais de livraison/expédition
      let deliveryFee = 0;
      const modeLivraison = document.getElementById('mode_livraison')?.value;
      
      if (modeLivraison === 'livraison') {
        const commune = document.getElementById('commune')?.value;
        if (commune === 'cocody') {
          deliveryFee = 1500;
        } else if (commune === 'dimbokro') {
          deliveryFee = 500;
        } else if (commune === 'autres') {
          deliveryFee = 2000;
        }
        
        const subtotalAfterDiscount = subtotal - discount + packagingPrice;
        if (subtotalAfterDiscount >= 50000) {
          deliveryFee = 0;
          html += `<div class="flex justify-between items-center py-2 text-green-300">
            <span>🚚 Livraison gratuite</span>
            <span>0 FCFA</span>
          </div>`;
        } else if (deliveryFee > 0) {
          let communeText = '';
          if (commune === 'cocody') communeText = 'Cocody';
          else if (commune === 'dimbokro') communeText = 'Dimbokro';
          else communeText = 'Autres communes';
          
          html += `<div class="flex justify-between items-center py-2">
            <span>Livraison ${communeText}</span>
            <span>+${deliveryFee.toLocaleString()} FCFA</span>
          </div>`;
        }
      } else if (modeLivraison === 'expedition') {
        deliveryFee = 3000;
        html += `<div class="flex justify-between items-center py-2">
          <span>Frais d'expédition</span>
          <span>+${deliveryFee.toLocaleString()} FCFA</span>
        </div>`;
      }

      // Remise paiement intégral
      let total = subtotal - discount + packagingPrice + deliveryFee;
      let paymentDiscount = 0;
      if (selectedPaymentMode === 'integral') {
        paymentDiscount = total * 0.02; // 2% au lieu de 5%
        html += `<div class="flex justify-between items-center py-2 text-green-300">
          <span>💰 Remise paiement (-2%)</span>
          <span>-${paymentDiscount.toLocaleString()} FCFA</span>
        </div>`;
      }

      total = total - paymentDiscount;

      html += `<div class="border-t border-white border-opacity-30 pt-4 mt-4">
        <div class="flex justify-between items-center text-xl font-bold">
          <span>TOTAL</span>
          <span>${total.toLocaleString()} FCFA</span>
        </div>
      </div>`;

      // Acompte si nécessaire
      if (selectedPaymentMode === 'acompte') {
        const acompte = total * 0.5;
        html += `<div class="bg-white bg-opacity-10 rounded-lg p-3 mt-4">
          <div class="flex justify-between text-sm">
            <span>Acompte à payer (50%)</span>
            <span class="font-bold">${acompte.toLocaleString()} FCFA</span>
          </div>
        </div>`;
      }

      summaryContent.innerHTML = html;
    }

    // Validation et soumission
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
      orderForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const totalQty = Object.keys(prices)
          .reduce((sum, product) => {
            const input = document.getElementById(`qty_${product}`);
            return sum + (input ? parseInt(input.value || 0) : 0);
          }, 0);
        
        if (totalQty === 0) {
          alert('Veuillez sélectionner au moins un produit.');
          return;
        }

        if (!selectedPaymentMode) {
          alert('Veuillez sélectionner un mode de paiement.');
          return;
        }

        const acceptTerms = document.getElementById('accept_terms');
        if (!acceptTerms || !acceptTerms.checked) {
          alert('Veuillez accepter les conditions de vente.');
          return;
        }

        // Animation du bouton
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
          const originalText = submitBtn.innerHTML;
          submitBtn.innerHTML = '<i class="ph ph-circle-notch animate-spin mr-2"></i>Envoi en cours...';
          submitBtn.disabled = true;
          
          // Timeout de sécurité pour rétablir le bouton en cas de problème
          setTimeout(() => {
            if (submitBtn.disabled) {
              submitBtn.innerHTML = originalText;
              submitBtn.disabled = false;
              alert('Erreur lors de l\'envoi. Veuillez réessayer ou nous contacter directement.');
            }
          }, 10000); // 10 secondes
        }

        // Soumission
        try {
          this.submit();
        } catch (error) {
          console.error('Erreur soumission:', error);
          if (submitBtn) {
            submitBtn.innerHTML = '<i class="ph ph-check-circle mr-2"></i>Confirmer ma commande';
            submitBtn.disabled = false;
          }
          alert('Erreur lors de l\'envoi. Veuillez réessayer.');
        }
      });
    }

    // Preloader
    window.addEventListener("load", function () {
      const preloader = document.getElementById('preloader');
      if (preloader) {
        setTimeout(() => {
          preloader.style.opacity = '0';
          setTimeout(() => {
            preloader.style.display = 'none';
          }, 500);
        }, 1000);
      }
    });

    // Traductions (système bilingue simplifié pour démo)
    const translations = {
      fr: {
        page_title: "Commande Poulets  des Fêtes 2025 - AGRIFORLAND RANCH",
        home: "Accueil",
        about: "À Propos", 
        poles: "Nos Pôles",
        projects: "Nos Projets",
        blog: "Blog",
        portfolios: "Portfolio",
        join_us: "Nous Rejoindre",
        contact_us: "Nous Contacter",
        hero_title: "Poulets  des Fêtes 2025",
        hero_subtitle: "AGRIFORLAND RANCH ",
        promo_badge: "-10% pour les 5 premiers clients !",
        step1_title: "Choisissez vos  coqs et coquelets",
        step2_title: "Vos informations", 
        step3_title: "Mode de paiement",
        order_summary: "Résumé de commande",
        submit_order: "Confirmer ma commande",
        order_success_title: "Commande enregistrée avec succès !",
        order_success_subtitle: "Merci pour votre confiance"
      },
      en: {
        page_title: "Male Chicken Christmas Order 2025 - AGRIFORLAND RANCH",
        home: "Home",
        about: "About",
        poles: "Our Divisions", 
        projects: "Our Projects",
        blog: "Blog",
        portfolios: "Portfolio",
        join_us: "Join Us",
        contact_us: "Contact Us",
        hero_title: "Male Christmas Chickens 2025",
        hero_subtitle: "AGRIFORLAND RANCH",
        promo_badge: "-10% for the first 5 customers!",
        step1_title: "Choose your male chickens",
        step2_title: "Your information",
        step3_title: "Payment method", 
        order_summary: "Order summary",
        submit_order: "Confirm my order",
        order_success_title: "Order successfully registered!",
        order_success_subtitle: "Thank you for your trust"
      }
    };

    // Gestionnaire de langue
    const languageSelectors = document.querySelectorAll('#language-selector, #language-selector-mobile');
    const languageIcons = document.querySelectorAll('#language-icon, #language-icon-mobile');

    function updateContent(lang) {
      document.querySelectorAll('[data-i18n]').forEach(element => {
        const key = element.getAttribute('data-i18n');
        if (translations[lang] && translations[lang][key]) {
          element.textContent = translations[lang][key];
        }
      });
      
      const titleElement = document.querySelector('title[data-i18n]');
      if (titleElement && translations[lang]) {
        document.title = translations[lang].page_title;
      }
      document.documentElement.lang = lang;
      
      languageIcons.forEach(icon => {
        if (icon) {
          icon.src = `../images/${lang}.webp`;
          icon.alt = lang === 'fr' ? 'Français' : 'English';
        }
      });
      
      languageSelectors.forEach(selector => {
        if (selector) {
          selector.value = lang;
        }
      });
    }

    languageSelectors.forEach(selector => {
      if (selector) {
        selector.addEventListener('change', (e) => {
          const selectedLang = e.target.value;
          updateContent(selectedLang);-
          localStorage.setItem('language', selectedLang);
        });
      }
    });

    // Langue par défaut
    const savedLang = localStorage.getItem('language') || 'fr';
    updateContent(savedLang);
  </script>
</body>
</html>