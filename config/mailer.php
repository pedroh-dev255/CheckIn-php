<?php

require __DIR__ . '/../vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$env = parse_ini_file('.env');

if ($env === false) {
    die("Erro ao carregar o arquivo .env");
}

function sendMail($to, $subject, $body) {
    global $env;
    // Configure aqui seu e-mail e senha de app
    $fromEmail = $env['EMAIL_MAIL'];
    $fromName = 'ClockIn - Suporte TI';
    $appPassword = $env['EMAIL_PASS'];

    $mail = new PHPMailer(true);

    try {
        // Configuração do servidor SMTP
        $mail->isSMTP();
        $mail->Host = $env['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $fromEmail;
        $mail->Password = $appPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Remetente
        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);

        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
        return false;
    }
}
?>
