<?php
header('Content-Type: application/json');
ob_start();
require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["cliente_nome"], $data["novo_vendedor_id"])) {
    echo json_encode(["success" => false, "message" => "Dados insuficientes."]);
    exit();
}

$cliente_nome = trim($data["cliente_nome"]);
$novo_vendedor_id = intval($data["novo_vendedor_id"]);

// Debug: Exibir nome do cliente recebido
error_log("Nome do cliente recebido: " . $cliente_nome);

// Buscar o ID do cliente pelo nome
$stmt = $conexao->prepare("SELECT id FROM clientes WHERE usuario = ?");
$stmt->bind_param("s", $cliente_nome);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Cliente não encontrado."]);
    error_log("Cliente não encontrado: " . $cliente_nome);
    exit();
}

$row = $result->fetch_assoc();
$cliente_id = $row["id"];
error_log("Cliente encontrado - ID: " . $cliente_id);

// Atualizar o vendedor_id na tabela clientes
$stmt = $conexao->prepare("UPDATE clientes SET vendedor_id = ? WHERE id = ?");
$stmt->bind_param("ii", $novo_vendedor_id, $cliente_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Vendedor alterado com sucesso."]);
    error_log("Vendedor alterado com sucesso para o cliente ID: " . $cliente_id);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao alterar vendedor."]);
    error_log("Erro ao executar UPDATE para o cliente ID: " . $cliente_id);
}

$stmt->close();
$conexao->close();
?>
