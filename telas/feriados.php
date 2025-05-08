<?php

    session_start();

    if(!isset($_SESSION['user'])) {
        header("Location: ../login.php");
        exit();
    }


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../styles/fav.png" type="image/x-icon">
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <title>Feriados</title>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="../styles/fav.png" alt="Logo">
        </div>
        <div class="user-info">
            <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['user']['nome']); ?></p>
        </div>
        <div class="menu">
            <a href="../">Home</a>
            <a href="./feriados.php">Feriados</a>
            <a href="./registroCsv.php">Importar Registros</a>
            <a href="./configs.php">Configurações</a>
        </div>
        <form action="../" method="get">
            <button type="submit" class="btn-logout" name="logout">Sair</button>
        </form>
    </div>
</body>
</html>