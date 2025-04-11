<?php
header('Content-Type: application/json');
require '../config.php';

try {
    // Lê os dados enviados em JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // Validação dos campos obrigatórios
    if (!isset($data["username"]) || !isset($data["plano"])) {
        throw new InvalidArgumentException("Campos 'username' e 'plano' são obrigatórios.");
    }

    $username = trim($data["username"]);
    $plano = trim($data["plano"]);

    if (empty($username) || empty($plano)) {
        echo json_encode(["success" => false, "message" => "Preencha todos os campos!"]);
        exit();
    }

    // Atualiza o plano do usuário na tabela de clientes (ajuste o nome da tabela se for diferente)
    $sqlUpdate = "UPDATE clientes SET plano = ? WHERE usuario = ?";
    $stmt = $conexao->prepare($sqlUpdate);

    if ($stmt === false) {
        throw new RuntimeException("Erro ao preparar a query: " . $conexao->error);
    }

    $stmt->bind_param("ss", $plano, $username);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Plano atualizado com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao atualizar o plano."]);
    }

    $stmt->close();

} catch (Exception $e) {
    error_log("Erro em alterar_plano.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno no servidor."]);
}
?>
