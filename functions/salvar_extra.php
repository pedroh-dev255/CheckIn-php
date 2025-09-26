<?php
session_start();

if(!isset($_SESSION['user'])) {
    header("Location: ./login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saldo50']) && isset($_POST['saldo100']) && isset($_POST['ref'])) {
        $saldo50 = $_POST['saldo50'];
        $saldo100 = $_POST['saldo100'];
        $ref = $_POST['ref'];

        require('../config/db.php');

        $stmt = $conn->prepare("INSERT INTO extras (id_usuario, ref, hora050, hora100, obs ) VALUES (?, ?, ?, ?, 'Salvo no botão')");
        $stmt->bind_param("isdd", $_SESSION['user']['id'], $ref, $saldo50, $saldo100); 
        // "isdd" -> int, string, double, double (ajuste se necessário)

        if ($stmt->execute()) {
            header("Location: ../index.php");
            exit();
        } else {
            echo "Erro ao salvar: " . $stmt->error;
        }

    } else {
        echo "Dados inválidos.";
        header("Location: ../index.php");
        exit();
    }
}

?>