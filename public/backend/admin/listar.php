<?php

header('Content-Type: application/json');
ob_start();

require '../config.php';

try {
    // Seleciona os usuários (considerar adicionar limites/paginação para muitos usuários)
    $sql = "SELECT usuario, status FROM clientes ORDER BY usuario ASC";
$stmt = $conexao->prepare($sql);


    // Usando prepare/execute mesmo sem parâmetros por consistência e segurança futura
    $stmt = $conexao->prepare($sql);
    if ($stmt === false) {
        throw new RuntimeException("Erro ao preparar a consulta: " . $conexao->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row; // Pode retornar só a lista de nomes: $users[] = $row['usuario'];
    }

    $stmt->close();

    // Retorna sucesso e os dados
    echo json_encode(["success" => true, "data" => $users]);

} catch (mysqli_sql_exception $e) {
    ob_end_clean();
    error_log("Erro de Banco de Dados (listar.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno no servidor [DB]."]);

} catch (Exception $e) {
    ob_end_clean();
    error_log("Erro Geral (listar.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno no servidor."]);

} finally {
    if (isset($conexao) && $conexao instanceof mysqli && $conexao->thread_id) {
       // $conexao->close();
    }
    ob_end_flush();
}
?>