<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../libraries/PHPMailer/src/Exception.php';
require __DIR__ . '/../libraries/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../libraries/PHPMailer/src/SMTP.php';

/**
 * Class Mailer
 *
 * Deze klasse handelt het verzenden van e-mails af via PHPMailer.
 * Alle instellingen voor SMTP worden hier geconfigureerd.
 */
class Mailer
{

    /**
     * Verstuurd een e-mail via PHPMailer.
     *
     * @param string $to      Het e-mailadres van de ontvanger
     * @param string $subject Het onderwerp van de e-mail
     * @param string $body    HTML-inhoud van de e-mail
     *
     * @return bool True als de mail succesvol is verzonden, anders false
     *
     * @throws Exception In geval van fouten binnen PHPMailer
     */
    public static function sendMail($to, $subject, $body)
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP-configuratie
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USER'];
            $mail->Password = $_ENV['MAIL_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['MAIL_PORT'];

            // SSL-opties (Mailtrap in sandbox vereist meestal dit)
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Afzender & ontvanger
            $mail->setFrom($_ENV['MAIL_FROM'] ?? 'no-reply@example.com', $_ENV['MAIL_FROM_NAME'] ?? 'Systeem');
            $mail->addAddress($to);

            // Mail-inhoud
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            return $mail->send();
        } catch (Exception $e) {
            error_log("Mail Error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
