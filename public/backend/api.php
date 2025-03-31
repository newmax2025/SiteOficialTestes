<?php
header('Content-Type: application/json');
ob_start();

// Inclui a configuração do banco de dados e outras configurações
require 'config.php';

// Defina a chave usada para buscar o token no seu banco de dados
define('TOKEN_DB_KEY', 'token_api');

try {
    // 1. Receber o CPF do frontend
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new InvalidArgumentException("Erro ao decodificar JSON recebido.");
    }

    if (!isset($inputData['cpf']) || empty(trim($inputData['cpf']))) {
        throw new InvalidArgumentException("CPF não informado.");
    }

    $cpfLimpo = preg_replace('/\D/', '', trim($inputData['cpf']));
    if (strlen($cpfLimpo) !== 11) {
         throw new InvalidArgumentException("Formato de CPF inválido.");
    }

    // 2. Buscar o Token da API no banco de dados local
    $token = null;
    $sqlToken = "SELECT valor FROM config WHERE chave = ?";
    $stmtToken = $conexao->prepare($sqlToken);
    if ($stmtToken === false) {
        throw new RuntimeException("Erro ao preparar consulta do token: " . $conexao->error);
    }
    $chaveToken = TOKEN_DB_KEY; // Usa a constante definida
    $stmtToken->bind_param("s", $chaveToken);
    $stmtToken->execute();
    $resultToken = $stmtToken->get_result();

    if ($rowToken = $resultToken->fetch_assoc()) {
        $token = $rowToken['valor'];
    }
    $stmtToken->close();

    if (empty($token)) {
        error_log("Token da API não encontrado no banco de dados para a chave: " . TOKEN_DB_KEY);
        throw new RuntimeException("Configuração interna do servidor incompleta [Token].");
    }

    // 3. Preparar e fazer a chamada para a API Externa (api.dbconsultas.com)
    $externalApiUrl = "https://api.dbconsultas.com/api/v1/{$token}/datalinkcpf/{$cpfLimpo}";

    // Usando cURL para a requisição externa (mais robusto)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $externalApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $externalApiResponse = curl_exec($ch);
    $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        error_log("Erro de cURL ao chamar API externa: " . $curlError);
        throw new RuntimeException("Erro ao comunicar com o serviço de consulta [cURL].");
    }

    if ($httpStatusCode >= 400) {
         $errorData = json_decode($externalApiResponse, true);
         $externalMessage = isset($errorData['message']) ? $errorData['message'] : "Serviço de consulta retornou erro {$httpStatusCode}.";
        throw new RuntimeException($externalMessage); // Repassa a mensagem de erro da API externa
    }

    ob_end_clean();
    echo $externalApiResponse;
    exit();

} catch (mysqli_sql_exception $e) {
    ob_end_clean();
    error_log("Erro de Banco de Dados (api.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno no servidor [DB]."]);

} catch (InvalidArgumentException $e) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);

} catch (RuntimeException $e) {
     ob_end_clean();
     http_response_code(500); 
     error_log("Erro de Runtime (api.php): " . $e->getMessage());
     echo json_encode(["success" => false, "message" => $e->getMessage()]);

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500); // Erro genérico
    error_log("Erro Geral (api.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno inesperado no servidor."]);

} finally {
    if (isset($conexao) && $conexao instanceof mysqli && $conexao->thread_id) {
        $conexao->close();
    }
}
?>