<?php
    date_default_timezone_set('America/Araguaina');

    $env = parse_ini_file('.env');

    if ($env === false) {
        die("Erro ao carregar o arquivo .env");
    }

    try {
        $conn = new mysqli($env['DB_HOST'], $env['DB_USER'], $env['DB_PASS'], $env['DB_NAME']);
        
        
        checkConnection($conn, '/');

        
        $conn->query("SET time_zone = '-03:00'");

    } catch (Exception $e) {
        header("Location: ./error.php");
        exit();
    }

    // Função para verificar a conexão (continua a mesma)
    function checkConnection($conn, $ponto) {
        if (!$conn) {
            header("Location: ".$ponto."./error.php");
            exit();
        }
    }
?>