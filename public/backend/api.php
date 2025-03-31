<?php
// Define o tipo de conteúdo como JSON
header('Content-Type: application/json');
// Inicia o buffer de saída
ob_start();

// Inclui a configuração do banco de dados e outras configurações
require 'config.php'; // Fornece $conexao

// Defina a chave usada para buscar o token no seu banco de dados
define('chave', 'valor'); // <-- AJUSTE SE O NOME DA CHAVE FOR DIFERENTE

try {
    // 1. Receber o CPF do frontend
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new InvalidArgumentException("Erro ao decodificar JSON recebido.");
    }

    if (!isset($inputData['cpf']) || empty(trim($inputData['cpf']))) {
        throw new InvalidArgumentException("CPF não informado.");
    }

    // Limpa e valida minimamente o CPF (somente dígitos)
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
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta como string
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout de 30 segundos
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Verifica o certificado SSL (importante!)
    // Adicionar headers se a API externa exigir (ex: User-Agent)
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array('HeaderName: HeaderValue'));

    $externalApiResponse = curl_exec($ch);
    $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // Pega o status HTTP retornado pela API externa
    $curlError = curl_error($ch); // Pega erros do cURL
    curl_close($ch);

    // 4. Verificar a resposta da API externa
    if ($curlError) {
        error_log("Erro de cURL ao chamar API externa: " . $curlError);
        throw new RuntimeException("Erro ao comunicar com o serviço de consulta [cURL].");
    }

    // Verifica o status HTTP retornado pela API externa
    // A API externa pode retornar erros (4xx, 5xx) que precisam ser tratados
    if ($httpStatusCode >= 400) {
         // Tenta decodificar a resposta de erro da API externa, se for JSON
         $errorData = json_decode($externalApiResponse, true);
         $externalMessage = isset($errorData['message']) ? $errorData['message'] : "Serviço de consulta retornou erro {$httpStatusCode}.";
        throw new RuntimeException($externalMessage); // Repassa a mensagem de erro da API externa
    }

    // 5. Retornar a resposta da API externa para o frontend
    // Simplesmente ecoamos a resposta que recebemos da API externa
    // O JavaScript frontend já sabe como interpretar essa resposta
    ob_end_clean(); // Limpa qualquer saída acidental antes de ecoar a resposta final
    echo $externalApiResponse;
    exit(); // Garante que nada mais seja executado

} catch (mysqli_sql_exception $e) {
    ob_end_clean();
    error_log("Erro de Banco de Dados (api.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno no servidor [DB]."]);

} catch (InvalidArgumentException $e) {
    ob_end_clean();
    http_response_code(400); // Bad Request para erros de entrada
    echo json_encode(["success" => false, "message" => $e->getMessage()]);

} catch (RuntimeException $e) {
     ob_end_clean();
     // Pode ser erro nosso (DB, cURL) ou erro reportado pela API externa
     http_response_code(500); // Internal Server Error ou erro da API externa
     error_log("Erro de Runtime (api.php): " . $e->getMessage()); // Loga o erro
     echo json_encode(["success" => false, "message" => $e->getMessage()]); // Retorna a mensagem da exceção

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500); // Erro genérico
    error_log("Erro Geral (api.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno inesperado no servidor."]);

} finally {
    if (isset($conexao) && $conexao instanceof mysqli && $conexao->thread_id) {
        // $conexao->close(); // Opcional
    }
    // Não usar ob_end_flush() aqui se já usamos echo direto na resposta da API
}
?>