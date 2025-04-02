<?php
header("Content-Type: application/json");
include "conexao.php"; // Conexão com o banco de dados

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['status'])) {
    echo json_encode(["success" => false, "message" => "Dados incompletos."]);
    exit;
}

$username = $data['username'];
$status = $data['status'];

// Validando se o status é permitido
if ($status !== "ativo" && $status !== "inativo") {
    echo json_encode(["success" => false, "message" => "Status inválido."]);
    exit;
}

// Atualizando o status do cliente
$sql = "UPDATE clientes SET status = ? WHERE usuario = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Erro ao preparar SQL: " . $conn->error);
    echo json_encode(["success" => false, "message" => "Erro interno no servidor."]);
    exit;
}

$stmt->bind_param("ss", $status, $username);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Status atualizado com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Nenhum usuário encontrado ou status já definido."]);
    }
} else {
    error_log("Erro ao executar SQL: " . $stmt->error);
    echo json_encode(["success" => false, "message" => "Erro ao atualizar status."]);
}

$stmt->close();
$conn->close();
?>
