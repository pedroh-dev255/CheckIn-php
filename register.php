<?php
    session_start();

    if (isset($_SESSION['user'])) {
        header("Location: ./index.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        include_once './config/db.php';

        echo $name = $_POST['name'];
        echo $email = $_POST['email'];
        echo $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (nome, email, senha) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            echo "<script>alert('Registro realizado com sucesso!');</script>";
            echo "<script>window.location.href = './login.php';</script>";
            exit();
        } else {
            echo "<script>alert('Erro ao registrar!');</script>";
        }
    }

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="./styles/register.css">
    <link rel="shortcut icon" href="./styles/fav.png" type="image/x-icon">
    
</head>
<body>
    <div class="register">
        <h1>Registrar</h1>
        <form action="./register.php" method="post">
            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit">Registrar</button>
        </form>
        <p>Já tem uma conta? <a href="./login.php">Entrar</a></p>

    </div>
</body>
</html>