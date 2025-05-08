<?php
session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(["erro" => "Acesso negado."]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$campo = $data['campo'] ?? null;
$valor = $data['valor'] ?? null;
$data_registro = $data['data'] ?? null;

$permitidos = ['obs', 'mode'];
if (!in_array($campo, $permitidos)) {
    http_response_code(400);
    echo json_encode(["erro" => "Campo inválido."]);
    exit;
}

require_once '../config/db.php';

// Verifica se já existe registro para a data
$stmt = $conn->prepare("SELECT id FROM registros WHERE id_usuario = ? AND data = ?");
$stmt->bind_param("is", $_SESSION['user']['id'], $data_registro);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    // Atualiza
    $stmt = $conn->prepare("UPDATE registros SET $campo = ? WHERE id_usuario = ? AND data = ?");
    $stmt->bind_param("sis", $valor, $_SESSION['user']['id'], $data_registro);
    $stmt->execute();
} else {
    // Cria novo registro
    $stmt = $conn->prepare("INSERT INTO registros (id_usuario, data, $campo) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_SESSION['user']['id'], $data_registro, $valor);
    $stmt->execute();
}

$conn->close();
echo json_encode(["sucesso" => true]);
