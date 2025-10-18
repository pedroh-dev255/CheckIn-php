<?php
session_start();

if(!isset($_SESSION['user'])) {
    header("Location: ./login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saldo50']) && isset($_POST['saldo100']) && isset($_POST['ref'])) {
        $saldo50 = $_POST['saldo50'] . ':00';
        $saldo100 = $_POST['saldo100'] . ':00';
        $ref = $_POST['ref'];

        echo 'Dados recebidos: Saldo 50 = ' . $saldo50 . ', Saldo 100 = ' . $saldo100 . ', Ref = ' . $ref;

        require('../config/db.php');

        // verifica se o ref já existe para o usuário
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM extras WHERE id_usuario = ? AND ref = ?");
        $checkStmt->bind_param("is", $_SESSION['user']['id'], $ref);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            // se ja existe faz update
            $updateStmt = $conn->prepare("UPDATE extras SET hora050 = ?, hora100 = ?, obs = 'Atualizado no botão' WHERE id_usuario = ? AND ref = ?");
            $updateStmt->bind_param("ssis", $saldo50, $saldo100, $_SESSION['user']['id'], $ref);
            if ($updateStmt->execute()) {
                header("Location: ../index.php");
                exit();
            } else {
                echo "Erro ao atualizar: " . $updateStmt->error;
            }
            $updateStmt->close();
            $conn->close();
        }
        // se não existe faz insert

        $stmt = $conn->prepare("INSERT INTO extras (id_usuario, ref, hora050, hora100, obs ) VALUES (?, ?, ?, ?, 'Salvo no botão')");
        $stmt->bind_param("isss", $_SESSION['user']['id'], $ref, $saldo50, $saldo100); 
        // "isdd" -> int, string, double, double (ajuste se necessário)

        if ($stmt->execute()) {
            header("Location: ../index.php");
            exit();
        } else {
            echo "Erro ao salvar: " . $stmt->error;
        }

    } else {
        header("Location: ../index.php");
        exit();
    }
}

?>