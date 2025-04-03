<?php
header('Content-Type: application/json');
require 'config.php';

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["cliente_nome"]) || !isset($data["novo_vendedor_id"])) {
        throw new InvalidArgumentException("Campos 'cliente' e 'vendedor_id' são obrigatórios.");
    }

    $cliente = trim($data["cliente"]);
    $vendedor_id = intval($data["vendedor_id"]); 

    if (empty($cliente) || empty($vendedor_id)) {
        echo json_encode(["success" => false, "message" => "Preencha todos os campos corretamente!"]);
        exit();
    }

    // Verifica se o cliente existe
    $sqlCheck = "SELECT usuario FROM clientes WHERE usuario = ?";
    $stmtCheck = $conexao->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $cliente);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Cliente não encontrado."]);
        exit();
    }
    $stmtCheck->close();

    // Verifica se o vendedor existe
    $sqlVendedor = "SELECT id FROM vendedores WHERE id = ?";
    $stmtVendedor = $conexao->prepare($sqlVendedor);
    $stmtVendedor->bind_param("i", $vendedor_id);
    $stmtVendedor->execute();
    $stmtVendedor->store_result();

    if ($stmtVendedor->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Vendedor não encontrado."]);
        exit();
    }
    $stmtVendedor->close();

    // Atualiza o vendedor do cliente
    $sqlUpdate = "UPDATE clientes SET vendedor_id = ? WHERE usuario = ?";
    $stmtUpdate = $conexao->prepare($sqlUpdate);
    $stmtUpdate->bind_param("is", $vendedor_id, $cliente);

    if ($stmtUpdate->execute()) {
        echo json_encode(["success" => true, "message" => "Vendedor atualizado com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao atualizar vendedor."]);
    }

    $stmtUpdate->close();

} catch (Exception $e) {
    error_log("Erro no mudar_vendedor.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno no servidor."]);
}
?>
