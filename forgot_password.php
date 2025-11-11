<?php
session_start();
include_once './config/db.php';

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
        // Gera token e data de expiração (1 hora)
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Remove tokens antigos
        $conn->prepare("DELETE FROM password_resets WHERE email = '$email'")->execute();

        // Insere o novo token
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expires);
        $stmt->execute();

        // Link (ajuste o domínio)
        $resetLink = "https://seusite.com/reset_password.php?token=$token";

        // Aqui você pode enviar o e-mail real com mail() ou PHPMailer
        echo "<script>alert('Um link de redefinição foi enviado para o e-mail informado. (Simulação)\\n$resetLink');</script>";
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
