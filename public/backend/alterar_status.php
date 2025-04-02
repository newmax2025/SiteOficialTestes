<?php
require_once "../config.php"; // Garante a conexão com o banco de dados

// Verifica se a requisição foi feita via POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Método inválido."]);
    exit;
}

// Obtém os dados da requisição
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["username"]) || !isset($data["status"])) {
    echo json_encode(["success" => false, "message" => "Dados incompletos."]);
    exit;
}

$username = trim($data["username"]);
$status = trim($data["status"]);

// Verifica se o status é válido (ativo/inativo)
if (!in_array($status, ["ativo", "inativo"])) {
    echo json_encode(["success" => false, "message" => "Status inválido."]);
    exit;
}

// Prepara a query para evitar SQL Injection
$sql = "UPDATE clientes SET status = ? WHERE usuario = ?";
$stmt = $conexao->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Erro na preparação da consulta."]);
    exit;
}

$stmt->bind_param("ss", $status, $username);
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Status atualizado com sucesso."]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao atualizar o status."]);
}

$stmt->close();
$conexao->close();
