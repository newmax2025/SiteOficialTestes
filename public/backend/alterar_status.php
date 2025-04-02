<?php
header("Content-Type: application/json");
include "../config.php"; // Conexão com o banco de dados

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['status'])) {
    echo json_encode(["success" => false, "message" => "Dados incompletos."]);
    exit;
}

$username = trim($data['username']); 
$status = trim($data['status']);

// Verificando se os valores não estão vazios
if (empty($username) || empty($status)) {
    echo json_encode(["success" => false, "message" => "Usuário ou status inválido."]);
    exit;
}

// Validando se o status é permitido
$allowed_status = ["ativo", "inativo"];
if (!in_array($status, $allowed_status)) {
    echo json_encode(["success" => false, "message" => "Status inválido."]);
    exit;
}

// Atualizando o status do cliente
$sql = "UPDATE clientes SET status = ? WHERE usuario = ?";
$stmt = $conexao->prepare($sql);

if (!$stmt) {
    error_log("Erro ao preparar SQL: " . $conexao->error);
    echo json_encode(["success" => false, "message" => "Erro interno no servidor."]);
    exit;
}

$stmt->bind_param("ss", $status, $username);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Status atualizado com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Nenhum usuário encontrado ou status já está atualizado."]);
    }
} else {
    error_log("Erro ao executar SQL: " . $stmt->error);
    echo json_encode(["success" => false, "message" => "Erro ao atualizar status."]);
}

$stmt->close();
$conexao->close();
?>
