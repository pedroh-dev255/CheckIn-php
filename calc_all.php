<?php

    session_start();

    if(!isset($_SESSION['user'])){
        exit();
    }

    require('./config/db.php');
    /*
    set_time_limit(0);
    ignore_user_abort(true);
    */

    $stmt = $conn->prepare("SELECT * FROM configs WHERE id_usuario = ?");
    $stmt->bind_param("i", $_SESSION['user']['id']);
    $stmt->execute();
    $configs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $data = [];
    foreach ($configs as $row) {
        $data[$row['nome']] = $row['valor'];
    }

    // Pega o mês e ano anterior ao atual
    $mes = date('m');
    $ano = date('Y');
    $fechamento = (int)$data['fechamento'];

    // Se o mês atual é janeiro, volta para dezembro do ano anterior
    if ($mes == 1) {
        $mes = 12;
        $ano--;
    } else {
        $mes--;
    }

    // Garante dois dígitos para o mês
    $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);

    // Monta a data de fechamento do mês anterior
    $datacalc = $ano . "-" . $mes . "-" . $fechamento;

    $stmt = $conn->prepare("SELECT * FROM registros WHERE id_usuario = ? AND data <= ?");
    $stmt->bind_param("is", $_SESSION['user']['id'], $datacalc);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();


    function timeToMinutes($time) {
        if (!$time || $time == '00:00') return 0;
        list($h, $m) = explode(':', $time);
        return ($h * 60) + $m; // ignora os segundos
    }


    function MinutsTohour($time) {
        if (!$time || $time == '00:00:00') return '00:00';
        $hours = floor($time / 60);
        $minutes = $time % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    
    


?>