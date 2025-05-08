<?php

    session_start();

    if(!isset($_SESSION['user'])) {
        header("Location: ../login.php");
        exit();
    }

    include_once '../config/db.php';

    $conf = "select * from configs where id_usuario = ?";
    $stmt = $conn->prepare($conf);
    $stmt->bind_param("i", $_SESSION['user']['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[$row['nome']] = $row['valor'];
        }
    }
    $stmt->close();


    $conf = "select * from nominal where id_usuario = ?";
    $stmt = $conn->prepare($conf);
    $stmt->bind_param("i", $_SESSION['user']['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $data_h = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data_h[$row['dia_semana']]['h1'] = $row['hora1'];
            $data_h[$row['dia_semana']]['h2'] = $row['hora2'];
            $data_h[$row['dia_semana']]['h3'] = $row['hora3'];
            $data_h[$row['dia_semana']]['h4'] = $row['hora4'];
            $data_h[$row['dia_semana']]['h5'] = $row['hora5'];
            $data_h[$row['dia_semana']]['h6'] = $row['hora6'];
        }
    }
    $stmt->close();





    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['tolerancia'])){
            $tol_ponto = $_POST['tol_ponto'];
            $tol_total = $_POST['tol_total'];
            $max_50 = $_POST['max_50'];

            if(!isset($data['toleranciaPonto'])){
                $stmt = $conn->prepare("INSERT INTO configs (id_usuario, nome, valor) VALUES (?, 'toleranciaPonto', ?)");
                $stmt->bind_param("is", $_SESSION['user']['id'], $tol_ponto);
                $stmt->execute();
            }else{
                $stmt = $conn->prepare("UPDATE configs SET valor = ? WHERE id_usuario = ? AND nome = 'toleranciaPonto'");
                $stmt->bind_param("si", $tol_ponto, $_SESSION['user']['id']);
                if($stmt->execute()){
                    echo "Atualizado com sucesso!";
                }else{
                    echo "Erro ao atualizar!";
                }
            }

            if(!isset($data['toleranciaGeral'])){
                $stmt = $conn->prepare("INSERT INTO configs (id_usuario, nome, valor) VALUES (?, 'toleranciaGeral', ?)");
                $stmt->bind_param("is", $_SESSION['user']['id'], $tol_total);
                $stmt->execute();
            }else{
                $stmt = $conn->prepare("UPDATE configs SET valor = ? WHERE id_usuario = ? AND nome = 'toleranciaGeral'");
                $stmt->bind_param("si", $tol_total, $_SESSION['user']['id']);
                $stmt->execute();
            }

            if(!isset($data['maximo50'])){
                $stmt = $conn->prepare("INSERT INTO configs (id_usuario, nome, valor) VALUES (?, 'maximo50', ?)");
                $stmt->bind_param("is", $_SESSION['user']['id'], $max_50);
                $stmt->execute();
            }else{
                $stmt = $conn->prepare("UPDATE configs SET valor = ? WHERE id_usuario = ? AND nome = 'maximo50'");
                $stmt->bind_param("si", $max_50, $_SESSION['user']['id']);
                $stmt->execute();
            }

            if(!isset($data['fechamento'])){
                $stmt = $conn->prepare("INSERT INTO configs (id_usuario, nome, valor) VALUES (?, 'fechamento', ?)");
                $stmt->bind_param("is", $_SESSION['user']['id'], $_POST['fechamento']);
                $stmt->execute();
            }else{
                $stmt = $conn->prepare("UPDATE configs SET valor = ? WHERE id_usuario = ? AND nome = 'fechamento'");
                $stmt->bind_param("si", $_POST['fechamento'], $_SESSION['user']['id']);
                $stmt->execute();
            }

            if(!isset($data['dt_inicio'])){
                $stmt = $conn->prepare("INSERT INTO configs (id_usuario, nome, valor) VALUES (?, 'dt_inicio', ?)");
                $stmt->bind_param("is", $_SESSION['user']['id'], $_POST['dt_inicio']);
                $stmt->execute();
            }else{
                $stmt = $conn->prepare("UPDATE configs SET valor = ? WHERE id_usuario = ? AND nome = 'dt_inicio'");
                $stmt->bind_param("si", $_POST['dt_inicio'], $_SESSION['user']['id']);
                $stmt->execute();
            }

            header("Location: ./configs.php");
            exit();
        }
        if(isset($_POST['horarios'])){
            echo $seg_horario1 = ($_POST['seg_horario1'] == '' || $_POST['seg_horario1'] == '00:00:00') ? NULL : $_POST['seg_horario1'];
            echo $seg_horario2 = ($_POST['seg_horario2'] == '' || $_POST['seg_horario2'] == '00:00:00') ? NULL : $_POST['seg_horario2'];
            echo $seg_horario3 = ($_POST['seg_horario3'] == '' || $_POST['seg_horario3'] == '00:00:00') ? NULL : $_POST['seg_horario3'];
            echo $seg_horario4 = ($_POST['seg_horario4'] == '' || $_POST['seg_horario4'] == '00:00:00') ? NULL : $_POST['seg_horario4'];
            echo $seg_horario5 = ($_POST['seg_horario5'] == '' || $_POST['seg_horario5'] == '00:00:00') ? NULL : $_POST['seg_horario5'];
            echo $seg_horario6 = ($_POST['seg_horario6'] == '' || $_POST['seg_horario6'] == '00:00:00') ? NULL : $_POST['seg_horario6'];

            echo $ter_horario1 = ($_POST['ter_horario1'] == '' || $_POST['ter_horario1'] == '00:00:00') ? NULL : $_POST['ter_horario1'];
            echo $ter_horario2 = ($_POST['ter_horario2'] == '' || $_POST['ter_horario2'] == '00:00:00') ? NULL : $_POST['ter_horario2'];
            echo $ter_horario3 = ($_POST['ter_horario3'] == '' || $_POST['ter_horario3'] == '00:00:00') ? NULL : $_POST['ter_horario3'];
            echo $ter_horario4 = ($_POST['ter_horario4'] == '' || $_POST['ter_horario4'] == '00:00:00') ? NULL : $_POST['ter_horario4'];
            echo $ter_horario5 = ($_POST['ter_horario5'] == '' || $_POST['ter_horario5'] == '00:00:00') ? NULL : $_POST['ter_horario5'];
            echo $ter_horario6 = ($_POST['ter_horario6'] == '' || $_POST['ter_horario6'] == '00:00:00') ? NULL : $_POST['ter_horario6'];

            echo $qua_horario1 = ($_POST['qua_horario1'] == '' || $_POST['qua_horario1'] == '00:00:00') ? NULL : $_POST['qua_horario1'];
            echo $qua_horario2 = ($_POST['qua_horario2'] == '' || $_POST['qua_horario2'] == '00:00:00') ? NULL : $_POST['qua_horario2'];
            echo $qua_horario3 = ($_POST['qua_horario3'] == '' || $_POST['qua_horario3'] == '00:00:00') ? NULL : $_POST['qua_horario3'];
            echo $qua_horario4 = ($_POST['qua_horario4'] == '' || $_POST['qua_horario4'] == '00:00:00') ? NULL : $_POST['qua_horario4'];
            echo $qua_horario5 = ($_POST['qua_horario5'] == '' || $_POST['qua_horario5'] == '00:00:00') ? NULL : $_POST['qua_horario5'];
            echo $qua_horario6 = ($_POST['qua_horario6'] == '' || $_POST['qua_horario6'] == '00:00:00') ? NULL : $_POST['qua_horario6'];

            echo $qui_horario1 = ($_POST['qui_horario1'] == '' || $_POST['qui_horario1'] == '00:00:00') ? NULL : $_POST['qui_horario1'];
            echo $qui_horario2 = ($_POST['qui_horario2'] == '' || $_POST['qui_horario2'] == '00:00:00') ? NULL : $_POST['qui_horario2'];
            echo $qui_horario3 = ($_POST['qui_horario3'] == '' || $_POST['qui_horario3'] == '00:00:00') ? NULL : $_POST['qui_horario3'];
            echo $qui_horario4 = ($_POST['qui_horario4'] == '' || $_POST['qui_horario4'] == '00:00:00') ? NULL : $_POST['qui_horario4'];
            echo $qui_horario5 = ($_POST['qui_horario5'] == '' || $_POST['qui_horario5'] == '00:00:00') ? NULL : $_POST['qui_horario5'];
            echo $qui_horario6 = ($_POST['qui_horario6'] == '' || $_POST['qui_horario6'] == '00:00:00') ? NULL : $_POST['qui_horario6'];

            echo $sex_horario1 = ($_POST['sex_horario1'] == '' || $_POST['sex_horario1'] == '00:00:00') ? NULL : $_POST['sex_horario1'];
            echo $sex_horario2 = ($_POST['sex_horario2'] == '' || $_POST['sex_horario2'] == '00:00:00') ? NULL : $_POST['sex_horario2'];
            echo $sex_horario3 = ($_POST['sex_horario3'] == '' || $_POST['sex_horario3'] == '00:00:00') ? NULL : $_POST['sex_horario3'];
            echo $sex_horario4 = ($_POST['sex_horario4'] == '' || $_POST['sex_horario4'] == '00:00:00') ? NULL : $_POST['sex_horario4'];
            echo $sex_horario5 = ($_POST['sex_horario5'] == '' || $_POST['sex_horario5'] == '00:00:00') ? NULL : $_POST['sex_horario5'];
            echo $sex_horario6 = ($_POST['sex_horario6'] == '' || $_POST['sex_horario6'] == '00:00:00') ? NULL : $_POST['sex_horario6'];

            echo $sab_horario1 = ($_POST['sab_horario1'] == '' || $_POST['sab_horario1'] == '00:00:00') ? NULL : $_POST['sab_horario1'];
            echo $sab_horario2 = ($_POST['sab_horario2'] == '' || $_POST['sab_horario2'] == '00:00:00') ? NULL : $_POST['sab_horario2'];
            echo $sab_horario3 = ($_POST['sab_horario3'] == '' || $_POST['sab_horario3'] == '00:00:00') ? NULL : $_POST['sab_horario3'];
            echo $sab_horario4 = ($_POST['sab_horario4'] == '' || $_POST['sab_horario4'] == '00:00:00') ? NULL : $_POST['sab_horario4'];
            echo $sab_horario5 = ($_POST['sab_horario5'] == '' || $_POST['sab_horario5'] == '00:00:00') ? NULL : $_POST['sab_horario5'];
            echo $sab_horario6 = ($_POST['sab_horario6'] == '' || $_POST['sab_horario6'] == '00:00:00') ? NULL : $_POST['sab_horario6'];

            echo $dom_horario1 = ($_POST['dom_horario1'] == '' || $_POST['dom_horario1'] == '00:00:00') ? NULL : $_POST['dom_horario1'];
            echo $dom_horario2 = ($_POST['dom_horario2'] == '' || $_POST['dom_horario2'] == '00:00:00') ? NULL : $_POST['dom_horario2'];
            echo $dom_horario3 = ($_POST['dom_horario3'] == '' || $_POST['dom_horario3'] == '00:00:00') ? NULL : $_POST['dom_horario3'];
            echo $dom_horario4 = ($_POST['dom_horario4'] == '' || $_POST['dom_horario4'] == '00:00:00') ? NULL : $_POST['dom_horario4'];
            echo $dom_horario5 = ($_POST['dom_horario5'] == '' || $_POST['dom_horario5'] == '00:00:00') ? NULL : $_POST['dom_horario5'];
            echo $dom_horario6 = ($_POST['dom_horario6'] == '' || $_POST['dom_horario6'] == '00:00:00') ? NULL : $_POST['dom_horario6'];


            if(!isset($data_h['Domingo'])){
                $stmt = $conn->prepare("INSERT INTO nominal (id_usuario, dia_semana, hora1, hora2, hora3, hora4, hora5, hora6) VALUES (?, 'Domingo', ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $_SESSION['user']['id'], $dom_horario1, $dom_horario2, $dom_horario3, $dom_horario4, $dom_horario5, $dom_horario6);
                $stmt->execute();
            }else {
                $stmt = $conn->prepare("UPDATE nominal SET hora1 = ?, hora2 = ?, hora3 = ?, hora4 = ?, hora5 = ?, hora6 = ? WHERE id_usuario = ? AND dia_semana = 'Domingo'");
                $stmt->bind_param("ssssssi", $dom_horario1, $dom_horario2, $dom_horario3, $dom_horario4, $dom_horario5, $dom_horario6, $_SESSION['user']['id']);
                $stmt->execute();
            }

            if(!isset($data_h['Segunda'])){
                $stmt = $conn->prepare("INSERT INTO nominal (id_usuario, dia_semana, hora1, hora2, hora3, hora4, hora5, hora6) VALUES (?, 'Segunda', ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $_SESSION['user']['id'], $seg_horario1, $seg_horario2, $seg_horario3, $seg_horario4, $seg_horario5, $seg_horario6);
                $stmt->execute();
            }else {
                $stmt = $conn->prepare("UPDATE nominal SET hora1 = ?, hora2 = ?, hora3 = ?, hora4 = ?, hora5 = ?, hora6 = ? WHERE id_usuario = ? AND dia_semana = 'Segunda'");
                $stmt->bind_param("ssssssi", $seg_horario1, $seg_horario2, $seg_horario3, $seg_horario4, $seg_horario5, $seg_horario6, $_SESSION['user']['id']);
                $stmt->execute();
            }

            if(!isset($data_h['Terça'])){
                $stmt = $conn->prepare("INSERT INTO nominal (id_usuario, dia_semana, hora1, hora2, hora3, hora4, hora5, hora6) VALUES (?, 'Terça', ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $_SESSION['user']['id'], $ter_horario1, $ter_horario2, $ter_horario3, $ter_horario4, $ter_horario5, $ter_horario6);
                $stmt->execute();
            }else {
                $stmt = $conn->prepare("UPDATE nominal SET hora1 = ?, hora2 = ?, hora3 = ?, hora4 = ?, hora5 = ?, hora6 = ? WHERE id_usuario = ? AND dia_semana = 'Terça'");
                $stmt->bind_param("ssssssi", $ter_horario1, $ter_horario2, $ter_horario3, $ter_horario4, $ter_horario5, $ter_horario6, $_SESSION['user']['id']);
                $stmt->execute();
            }

            if(!isset($data_h['Quarta'])){
                $stmt = $conn->prepare("INSERT INTO nominal (id_usuario, dia_semana, hora1, hora2, hora3, hora4, hora5, hora6) VALUES (?, 'Quarta', ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $_SESSION['user']['id'], $qua_horario1, $qua_horario2, $qua_horario3, $qua_horario4, $qua_horario5, $qua_horario6);
                $stmt->execute();
            }else {
                $stmt = $conn->prepare("UPDATE nominal SET hora1 = ?, hora2 = ?, hora3 = ?, hora4 = ?, hora5 = ?, hora6 = ? WHERE id_usuario = ? AND dia_semana = 'Quarta'");
                $stmt->bind_param("ssssssi", $qua_horario1, $qua_horario2, $qua_horario3, $qua_horario4, $qua_horario5, $qua_horario6, $_SESSION['user']['id']);
                $stmt->execute();
            }

            if(!isset($data_h['Quinta'])){
                $stmt = $conn->prepare("INSERT INTO nominal (id_usuario, dia_semana, hora1, hora2, hora3, hora4, hora5, hora6) VALUES (?, 'Quinta', ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $_SESSION['user']['id'], $qui_horario1, $qui_horario2, $qui_horario3, $qui_horario4, $qui_horario5, $qui_horario6);
                $stmt->execute();
            }else {
                $stmt = $conn->prepare("UPDATE nominal SET hora1 = ?, hora2 = ?, hora3 = ?, hora4 = ?, hora5 = ?, hora6 = ? WHERE id_usuario = ? AND dia_semana = 'Quinta'");
                $stmt->bind_param("ssssssi", $qui_horario1, $qui_horario2, $qui_horario3, $qui_horario4, $qui_horario5, $qui_horario6, $_SESSION['user']['id']);
                $stmt->execute();
            }

            if(!isset($data_h['Sexta'])){
                $stmt = $conn->prepare("INSERT INTO nominal (id_usuario, dia_semana, hora1, hora2, hora3, hora4, hora5, hora6) VALUES (?, 'Sexta', ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $_SESSION['user']['id'], $sex_horario1, $sex_horario2, $sex_horario3, $sex_horario4, $sex_horario5, $sex_horario6);
                $stmt->execute();
            }else {
                $stmt = $conn->prepare("UPDATE nominal SET hora1 = ?, hora2 = ?, hora3 = ?, hora4 = ?, hora5 = ?, hora6 = ? WHERE id_usuario = ? AND dia_semana = 'Sexta'");
                $stmt->bind_param("ssssssi", $sex_horario1, $sex_horario2, $sex_horario3, $sex_horario4, $sex_horario5, $sex_horario6, $_SESSION['user']['id']);
                $stmt->execute();
            }

            if(!isset($data_h['Sábado'])){
                $stmt = $conn->prepare("INSERT INTO nominal (id_usuario, dia_semana, hora1, hora2, hora3, hora4, hora5, hora6) VALUES (?, 'Sábado', ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $_SESSION['user']['id'], $sab_horario1, $sab_horario2, $sab_horario3, $sab_horario4, $sab_horario5, $sab_horario6);
                $stmt->execute();
            }else {
                $stmt = $conn->prepare("UPDATE nominal SET hora1 = ?, hora2 = ?, hora3 = ?, hora4 = ?, hora5 = ?, hora6 = ? WHERE id_usuario = ? AND dia_semana = 'Sábado'");
                $stmt->bind_param("ssssssi", $sab_horario1, $sab_horario2, $sab_horario3, $sab_horario4, $sab_horario5, $sab_horario6, $_SESSION['user']['id']);
                $stmt->execute();
            }

            header("Location: ./configs.php");
            exit();
        }

    }

    $conn->close();


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../styles/fav.png" type="image/x-icon">
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="stylesheet" href="../styles/navbar.css">
    <title>Configs</title>
    <style>
        .container {
            display: flex;
            flex-direction: column;
        }
    </style>
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
    <div class="container">
        <div class="filtro">
            <h1>Configurações</h1>
            <form action="./configs.php" method="post">
                <h2>Tolerancias</h2>
                <input type="hidden" name="tolerancia" value="1">
                <label for="tol_ponto">Tolerância por ponto(minutos):</label>
                <input type="number" id="tol_ponto" name="tol_ponto" value="<?php if(isset($data['toleranciaPonto'])){ echo $data['toleranciaPonto'];} ?>" required><br><br>

                <label for="tol_total">Tolerância no total(minutos):</label>
                <input type="number" id="tol_total" name="tol_total" value="<?php if(isset($data['toleranciaGeral'])){echo $data['toleranciaGeral']; }?>" required><br><br>

                <label for="max_50">Maximo de horas Extras 50% por dia(minutos):</label>
                <input type="number" id="max_50" name="max_50" value="<?php if(isset($data['maximo50'])){echo $data['maximo50'];} ?>" required><br><br>

                <label for="fechamento">Dia que o ponto fecha</label>
                <input type="number" name="fechamento" id="fechamento" value="<?php if(isset($data['fechamento'])){echo $data['fechamento'];} ?>" required><br><br>

                <label for="dt_inicio">Data de Inicio</label>
                <input type="date" name="dt_inicio" id="dt_inicio" value="<?php if(isset($data['dt_inicio'])){echo $data['dt_inicio'];} ?>" required><br>
                <i style="font-size: 10px">Apartir desta data o sistema vai calcular o banco de horas</i><br><br>

                <button class="btn-submit" type="submit">Atualizar</button>
            </form>

            <form action="./configs.php" method="post">
                <h2>Horarios nominais</h2>
                <input type="hidden" name="horarios" value="1">
                <label for="horarios">Segunda-feira:</label>
                <input type="time" id="horarios" name="seg_horario1" value="<?php if(isset($data_h['Segunda']['h1'])){echo $data_h['Segunda']['h1'];} ?>">
                <input type="time" id="horarios" name="seg_horario2" value="<?php if(isset($data_h['Segunda']['h2'])){echo $data_h['Segunda']['h2'];} ?>">
                <input type="time" id="horarios" name="seg_horario3" value="<?php if(isset($data_h['Segunda']['h3'])){echo $data_h['Segunda']['h3'];} ?>">
                <input type="time" id="horarios" name="seg_horario4" value="<?php if(isset($data_h['Segunda']['h4'])){echo $data_h['Segunda']['h4'];} ?>">
                <input type="time" id="horarios" name="seg_horario5" value="<?php if(isset($data_h['Segunda']['h5'])){echo $data_h['Segunda']['h5'];} ?>">
                <input type="time" id="horarios" name="seg_horario6" value="<?php if(isset($data_h['Segunda']['h6'])){echo $data_h['Segunda']['h6'];} ?>">
                <br><br>
                <label for="horarios">Terça-feira:</label>
                <input type="time" id="horarios" name="ter_horario1" value="<?php if(isset($data_h['Terça']['h1'])){echo $data_h['Terça']['h1'];} ?>">
                <input type="time" id="horarios" name="ter_horario2" value="<?php if(isset($data_h['Terça']['h2'])){echo $data_h['Terça']['h2'];} ?>">
                <input type="time" id="horarios" name="ter_horario3" value="<?php if(isset($data_h['Terça']['h3'])){echo $data_h['Terça']['h3'];} ?>">
                <input type="time" id="horarios" name="ter_horario4" value="<?php if(isset($data_h['Terça']['h4'])){echo $data_h['Terça']['h4'];} ?>">
                <input type="time" id="horarios" name="ter_horario5" value="<?php if(isset($data_h['Terça']['h5'])){echo $data_h['Terça']['h5'];} ?>">
                <input type="time" id="horarios" name="ter_horario6" value="<?php if(isset($data_h['Terça']['h6'])){echo $data_h['Terça']['h6'];} ?>">
                <br><br>
                <label for="horarios">Quarta-feira:</label>
                <input type="time" id="horarios" name="qua_horario1" value="<?php if(isset($data_h['Quarta']['h1'])){echo $data_h['Quarta']['h1'];} ?>">
                <input type="time" id="horarios" name="qua_horario2" value="<?php if(isset($data_h['Quarta']['h2'])){echo $data_h['Quarta']['h2'];} ?>">
                <input type="time" id="horarios" name="qua_horario3" value="<?php if(isset($data_h['Quarta']['h3'])){echo $data_h['Quarta']['h3'];} ?>">
                <input type="time" id="horarios" name="qua_horario4" value="<?php if(isset($data_h['Quarta']['h4'])){echo $data_h['Quarta']['h4'];} ?>">
                <input type="time" id="horarios" name="qua_horario5" value="<?php if(isset($data_h['Quarta']['h5'])){echo $data_h['Quarta']['h5'];} ?>">
                <input type="time" id="horarios" name="qua_horario6" value="<?php if(isset($data_h['Quarta']['h6'])){echo $data_h['Quarta']['h6'];} ?>">
                <br><br>
                <label for="horarios">Quinta-feira:</label>
                <input type="time" id="horarios" name="qui_horario1" value="<?php if(isset($data_h['Quinta']['h1'])){echo $data_h['Quinta']['h1'];} ?>">
                <input type="time" id="horarios" name="qui_horario2" value="<?php if(isset($data_h['Quinta']['h2'])){echo $data_h['Quinta']['h2'];} ?>">
                <input type="time" id="horarios" name="qui_horario3" value="<?php if(isset($data_h['Quinta']['h3'])){echo $data_h['Quinta']['h3'];} ?>">
                <input type="time" id="horarios" name="qui_horario4" value="<?php if(isset($data_h['Quinta']['h4'])){echo $data_h['Quinta']['h4'];} ?>">
                <input type="time" id="horarios" name="qui_horario5" value="<?php if(isset($data_h['Quinta']['h5'])){echo $data_h['Quinta']['h5'];} ?>">
                <input type="time" id="horarios" name="qui_horario6" value="<?php if(isset($data_h['Quinta']['h6'])){echo $data_h['Quinta']['h6'];} ?>">
                <br><br>
                <label for="horarios">Sexta-feira:</label>
                <input type="time" id="horarios" name="sex_horario1" value="<?php if(isset($data_h['Sexta']['h1'])){echo $data_h['Sexta']['h1'];} ?>">
                <input type="time" id="horarios" name="sex_horario2" value="<?php if(isset($data_h['Sexta']['h2'])){echo $data_h['Sexta']['h2'];} ?>">
                <input type="time" id="horarios" name="sex_horario3" value="<?php if(isset($data_h['Sexta']['h3'])){echo $data_h['Sexta']['h3'];} ?>">
                <input type="time" id="horarios" name="sex_horario4" value="<?php if(isset($data_h['Sexta']['h4'])){echo $data_h['Sexta']['h4'];} ?>">
                <input type="time" id="horarios" name="sex_horario5" value="<?php if(isset($data_h['Sexta']['h5'])){echo $data_h['Sexta']['h5'];} ?>">
                <input type="time" id="horarios" name="sex_horario6" value="<?php if(isset($data_h['Sexta']['h6'])){echo $data_h['Sexta']['h6'];} ?>">
                <br><br>
                <label for="horarios">Sábado:</label>
                <input type="time" id="horarios" name="sab_horario1" value="<?php if(isset($data_h['Sábado']['h1'])){echo $data_h['Sábado']['h1'];} ?>">
                <input type="time" id="horarios" name="sab_horario2" value="<?php if(isset($data_h['Sábado']['h2'])){echo $data_h['Sábado']['h2'];} ?>">
                <input type="time" id="horarios" name="sab_horario3" value="<?php if(isset($data_h['Sábado']['h3'])){echo $data_h['Sábado']['h3'];} ?>">
                <input type="time" id="horarios" name="sab_horario4" value="<?php if(isset($data_h['Sábado']['h4'])){echo $data_h['Sábado']['h4'];} ?>">
                <input type="time" id="horarios" name="sab_horario5" value="<?php if(isset($data_h['Sábado']['h5'])){echo $data_h['Sábado']['h5'];} ?>">
                <input type="time" id="horarios" name="sab_horario6" value="<?php if(isset($data_h['Sábado']['h6'])){echo $data_h['Sábado']['h6'];} ?>">
                <br><br>
                <label for="horarios">Domingo:</label>
                <input type="time" id="horarios" name="dom_horario1" value="<?php if(isset($data_h['Domingo']['h1'])){echo $data_h['Domingo']['h1'];} ?>">
                <input type="time" id="horarios" name="dom_horario2" value="<?php if(isset($data_h['Domingo']['h2'])){echo $data_h['Domingo']['h2'];} ?>">
                <input type="time" id="horarios" name="dom_horario3" value="<?php if(isset($data_h['Domingo']['h3'])){echo $data_h['Domingo']['h3'];} ?>">
                <input type="time" id="horarios" name="dom_horario4" value="<?php if(isset($data_h['Domingo']['h4'])){echo $data_h['Domingo']['h4'];} ?>">
                <input type="time" id="horarios" name="dom_horario5" value="<?php if(isset($data_h['Domingo']['h5'])){echo $data_h['Domingo']['h5'];} ?>">
                <input type="time" id="horarios" name="dom_horario6" value="<?php if(isset($data_h['Domingo']['h6'])){echo $data_h['Domingo']['h6'];} ?>">
            
                <br><br>

                <button class="btn-submit" type="submit">Atualizar</button>
            </form>

        </div>
    </div>
</body>
</html>