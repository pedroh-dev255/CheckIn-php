<?php
session_start();
require_once './config/db.php';

// Verifica autenticação
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit('Não autorizado');
}

$idUsuario  = $_SESSION['user']['id'];
$data       = $_POST['data']      ?? null;
$campo      = $_POST['campo']     ?? null;
$valorTime  = $_POST['valorTime'] ?? null;

if (!$data || !$campo) {
    http_response_code(400);
    exit('Parâmetros faltando');
}

// Valida formato HH:MM
if (!preg_match('/^\d{2}:\d{2}$/', $valorTime)) {
    http_response_code(400);
    exit('Formato de hora inválido');
}

// Normaliza para HH:MM:SS
$valorTimeFull = $valorTime . ':00';

// Verifica existência de registro para a data
$stmt = $conn->prepare(
    "SELECT id FROM registros WHERE id_usuario = ? AND data = ?"
);
$stmt->bind_param("is", $idUsuario, $data);
$stmt->execute();
$stmt->bind_result($idRegistro);
$stmt->fetch();
$stmt->close();

if ($idRegistro) {
    // Atualiza campo existente
    $sql = "UPDATE registros SET {$campo} = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $valorTimeFull, $idRegistro);
} else {
    // Insere novo registro
    $sql = "INSERT INTO registros (id_usuario, data, {$campo}) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $idUsuario, $data, $valorTimeFull);
}

if (!$stmt->execute()) {
    http_response_code(500);
    exit('Erro no banco: ' . $stmt->error);
}

echo 'ok';
