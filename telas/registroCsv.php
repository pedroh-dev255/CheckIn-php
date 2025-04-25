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
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <title>Importar Registros</title>
    <style>
        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        .form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            margin-bottom: 10px;
        }
        input[type="file"] {
            margin-bottom: 20px;
        }
        .enviar {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="navbar">
        <div class="logo">
            <img src="../styles/fav.png" alt="Logo">
        </div>
        <div class="user-info">
            <p>Bem-vindo, <?php echo $_SESSION['user']['nome']; ?></p>
        </div>
        <div class="menu">
            <a href="../">Home</a>
            <a href="./historico.php">Histórico</a>
            <a href="./registroCsv.php">Importar Registros</a>
            <a href="./configuracoes.php">Configurações</a>
        </div>
        <form action="../" method="get">
            <button type="submit" class="btn-logout" name="logout">Sair</button>
        </form>
    </div>
    <div class="container">
        <h1>Importar Registros</h1>
        <form class="form" action="../config/importarCsv.php" method="post" enctype="multipart/form-data">
            <label for="csvFile">Selecione o arquivo CSV:</label>
            <input type="file" name="csvFile" id="csvFile" accept=".csv" required> <br>
            <i style="font-size: 12px;">Formato aceito: Data, Hora 1, Hora 2, Hora 3, Hora 4, Hora 5, hora 6 e obs</i><br><br>
            <button class="enviar" type="submit">Importar</button>
        </form>

        <?php
            if (isset($_GET['success'])) {
                echo "<p>Arquivo importado com sucesso!</p>";
            } elseif (isset($_GET['error'])) {
                echo "<p>Erro ao importar o arquivo.</p>";
            }
        ?>
    </div>
</body>
</html>