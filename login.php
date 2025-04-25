<?php
    session_start();

    if (isset($_SESSION['user'])) {
        header("Location: ./index.php");
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        include_once './config/db.php';

        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['senha'])) {
                $_SESSION['user'] = $user;
                header("Location: ./");
                exit();
            } else {
                echo "<script>alert('Senha incorreta!');</script>";
            }
        } else {
            echo "<script>alert('Email não encontrado!');</script>";
        }
    }

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./styles/login.css">
    <link rel="shortcut icon" href="./styles/fav.png" type="image/x-icon">
</head>
<body>
    <div class="login">
        <h1>Login</h1>
        <form action="./login.php" method="post">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required><br><br>

            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit">Entrar</button>
        </form>
        <p>Não tem uma conta? <a href="./register.php">Registrar</a></p>

    </div>
</body>
</html>