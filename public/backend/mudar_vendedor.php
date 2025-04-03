<?php
header('Content-Type: application/json');
ob_start(); // Inicia buffer de saída

require 'config.php';

try {
    // Obtém os dados enviados via JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // Verifica se o JSON foi decodificado corretamente
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new InvalidArgumentException("Erro ao decodificar JSON.");
    }

    // Verifica se os campos esperados foram recebidos
    if (!isset($data["cliente"]) || !isset($data["novo_vendedor"])) {
        throw new InvalidArgumentException("Campos 'cliente' e 'novo_vendedor' são obrigatórios.");
    }

    $cliente = trim($data["cliente"]);
    $novo_vendedor = trim($data["novo_vendedor"]);

    // Validação básica
    if (empty($cliente) || empty($novo_vendedor)) {
        echo json_encode(["success" => false, "message" => "Preencha todos os campos!"]);
        exit();
    }

    // Verifica se o cliente existe
    $sqlCheck = "SELECT id FROM clientes WHERE usuario = ?";
    $stmtCheck = $conexao->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $cliente);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows === 0) {
        error_log("Cliente não encontrado: " . $cliente);
        $stmtCheck->close();
        echo json_encode(["success" => false, "message" => "Cliente não encontrado."]);
        exit();
    }
    $stmtCheck->close();

    // Atualiza o vendedor do cliente
    $sqlUpdate = "UPDATE clientes SET vendedor_id = (SELECT id FROM vendedores WHERE nome = ?) WHERE usuario = ?";
    $stmtUpdate = $conexao->prepare($sqlUpdate);

    if ($stmtUpdate === false) {
        error_log("Erro ao preparar o UPDATE: " . $conexao->error);
        throw new RuntimeException("Erro ao preparar a atualização: " . $conexao->error);
    }

    $stmtUpdate->bind_param("ss", $novo_vendedor, $cliente);

    if ($stmtUpdate->execute()) {
        error_log("Vendedor atualizado para o cliente: " . $cliente . " - Novo Vendedor: " . $novo_vendedor);
        echo json_encode(["success" => true, "message" => "Vendedor atualizado com sucesso!"]);
    } else {
        error_log("Erro ao executar UPDATE para cliente: " . $cliente . " - Erro: " . $stmtUpdate->error);
        echo json_encode(["success" => false, "message" => "Erro ao atualizar vendedor."]);
    }

    $stmtUpdate->close();

} catch (mysqli_sql_exception $e) {
    ob_end_clean();
    error_log("Erro MySQL (muda_vendedor.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno no servidor [DB]."]);

} catch (InvalidArgumentException $e) {
    ob_end_clean();
    echo json_encode(["success" => false, "message" => $e->getMessage()]);

} catch (Exception $e) {
    ob_end_clean();
    error_log("Erro Geral (muda_vendedor.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno no servidor."]);

} finally {
    ob_end_flush();
}
?>
