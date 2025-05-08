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
            $stmt = $conn->prepare("select id from users where email = ?");
            $stmt->bind_param("s",$email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $stmt = $conn->prepare("INSERT INTO checkin.configs (id_usuario, nome, valor) VALUES
            (?,'toleranciaPonto', '5'),
            (?,'toleranciaGeral', '10'),
            (?,'maximo50', '180'),
            (?, 'fechamento', '25');");
            $stmt->bind_param("iiii", $user['id'], $user['id'], $user['id'], $user['id']);
            $stmt->execute();

            $stmt = $conn->prepare("INSERT INTO checkin.nominal (id_usuario, dia_semana, hora1, hora2, hora3, hora4, hora5, hora6) VALUES
            (?,'Domingo', '8:00', '12:00', '14:00', '18:00', NULL, NULL),
            (?,'Segunda', '8:00', '12:00', '14:00', '18:00', NULL, NULL),
            (?,'Terça', '8:00', '12:00', '14:00', '18:00', NULL, NULL),
            (?,'Quarta', '8:00', '12:00', '14:00', '18:00', NULL, NULL),
            (?,'Quinta', '8:00', '12:00', '14:00', '18:00', NULL, NULL),
            (?,'Sexta', '8:00', '12:00', '14:00', '18:00', NULL, NULL),
            (?,'Sábado','8:00','12:00',null,null,NULL,NULL);");
            $stmt->bind_param("iiiiiii", $user['id'], $user['id'], $user['id'], $user['id'], $user['id'], $user['id'], $user['id']);
            $stmt->execute();

            header("Location: ./login.php");
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