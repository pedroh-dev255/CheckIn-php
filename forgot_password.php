<?php
session_start();
include_once './config/db.php';
include_once './config/mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Verifica se o e-mail existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('E-mail não encontrado.');</script>";
    } else {
        // Gera token e expiração
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Remove tokens antigos
        $conn->prepare("DELETE FROM password_resets WHERE email = '$email'")->execute();

        // Insere o novo token
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expires);
        $stmt->execute();

        // Gera link (ajuste o domínio do seu site)
        $resetLink = "https://seusite.com/reset_password.php?token=$token";

        // Corpo do e-mail (HTML)
        $body = "
        <h2>Recuperação de Senha - ClockIn</h2>
        <p>Olá,</p>
        <p>Recebemos uma solicitação para redefinir a sua senha.</p>
        <p>Para redefinir, clique no botão abaixo:</p>
        <p><a href='$resetLink' style='background:#4CAF50;color:white;padding:10px 20px;border-radius:5px;text-decoration:none;'>Redefinir Senha</a></p>
        <br>
        <p>Se você não fez essa solicitação, ignore este e-mail.</p>
        <p><small>Este link expira em 1 hora.</small></p>
        ";

        if (sendMail($email, 'Recuperação de senha - ClockIn', $body)) {
            echo "<script>alert('Um e-mail de redefinição foi enviado. Verifique sua caixa de entrada.'); window.location.href='./login.php';</script>";
        } else {
            echo "<script>alert('Erro ao enviar o e-mail. Tente novamente mais tarde.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="./styles/login.css">
</head>
<body>
    <div class="login">
        <h1>Recuperar Senha</h1>
        <form action="" method="post">
            <label for="email">E-mail cadastrado:</label>
            <input type="email" id="email" name="email" required><br><br>
            <button type="submit">Enviar link de redefinição</button>
        </form>
        <p><a href="./login.php">Voltar ao login</a></p>
    </div>
</body>
</html>
