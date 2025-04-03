<?php
session_start();
header('Content-Type: application/json');
require 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["cliente_id"], $data["novo_vendedor_id"])) {
    echo json_encode(["success" => false, "message" => "Dados insuficientes."]);
    exit();
}

$cliente_id = intval($data["cliente_id"]);
$novo_vendedor_id = intval($data["novo_vendedor_id"]);

$stmt = $conexao->prepare("UPDATE clientes SET vendedor_id = ? WHERE id = ?");
$stmt->bind_param("ii", $novo_vendedor_id, $cliente_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Vendedor alterado com sucesso."]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao alterar vendedor."]);
}

$stmt->close();
$conexao->close();
?>
