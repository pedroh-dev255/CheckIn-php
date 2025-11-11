<?php
include_once './config/db.php';

if (!isset($_GET['token'])) {
    die('Token inválido.');
}

$token = $_GET['token'];

// Verifica token válido e não expirado
$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Token inválido ou expirado.');
}

$resetData = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Atualiza senha do usuário
    $stmt = $conn->prepare("UPDATE users SET senha = ? WHERE email = ?");
    $stmt->bind_param("ss", $newPass, $resetData['email']);
    $stmt->execute();

    // Remove token usado
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->bind_param("s", $resetData['email']);
    $stmt->execute();

    echo "<script>alert('Senha redefinida com sucesso!'); window.location.href='./login.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="./styles/login.css">
</head>
<body>
    <div class="login">
        <h1>Nova Senha</h1>
        <form method="post">
            <label for="password">Digite sua nova senha:</label>
            <input type="password" id="password" name="password" required><br><br>
            <button type="submit">Redefinir</button>
        </form>
    </div>
</body>
</html>
