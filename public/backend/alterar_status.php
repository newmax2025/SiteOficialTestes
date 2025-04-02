<?php
header("Content-Type: application/json");
include "conexao.php"; // Certifique-se de que a conexão com o banco está configurada

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['status'])) {
    echo json_encode(["success" => false, "message" => "Dados incompletos."]);
    exit;
}

$username = $data['username'];
$status = $data['status'];

if ($status !== "ativo" && $status !== "inativo") {
    echo json_encode(["success" => false, "message" => "Status inválido."]);
    exit;
}

$sql = "UPDATE clientes SET status = ? WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $status, $username);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Status atualizado com sucesso!"]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao atualizar status."]);
}

$stmt->close();
$conn->close();
?>
