<?php
// Define o tipo de conteúdo como JSON
header('Content-Type: application/json');
// Inicia o buffer de saída
ob_start();

// Inclui a configuração do banco de dados e outras configurações
require 'config.php'; // Fornece $conexao

// Chaves usadas para buscar configurações no banco de dados
define('TOKEN_DB_KEY', 'token_api'); // Chave para o token da API dbconsultas (CORRIGIDO)
define('CLOUDFLARE_SECRET_KEY_DB', 'cloudflare_secret'); // <-- Chave para a Chave Secreta

try {
    // 1. Receber CPF e Token CAPTCHA do frontend
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new InvalidArgumentException("Erro ao decodificar JSON recebido.");
    }

    // Valida CPF
    if (!isset($inputData['cpf']) || empty(trim($inputData['cpf']))) {
        throw new InvalidArgumentException("CPF não informado.");
    }
    $cpfLimpo = preg_replace('/\D/', '', trim($inputData['cpf']));
    if (strlen($cpfLimpo) !== 11) {
         throw new InvalidArgumentException("Formato de CPF inválido.");
    }

    // --- NOVO: Valida Token CAPTCHA ---
    if (!isset($inputData['captchaToken']) || empty(trim($inputData['captchaToken']))) {
        throw new InvalidArgumentException("Token CAPTCHA não informado.");
    }
    $captchaToken = trim($inputData['captchaToken']);
    // --- FIM NOVO ---

    // ---------------------------------------------------
    // --- NOVO: Verificação do CAPTCHA com Cloudflare ---
    // ---------------------------------------------------

    // 2. Buscar a Chave Secreta do Cloudflare no banco de dados local
    $cloudflareSecretKey = null;
    $sqlCfKey = "SELECT valor FROM config WHERE chave = ?";
    $stmtCfKey = $conexao->prepare($sqlCfKey);
    if ($stmtCfKey === false) {
        throw new RuntimeException("Erro ao preparar consulta da chave secreta: " . $conexao->error);
    }
    $chaveCfDb = CLOUDFLARE_SECRET_KEY_DB; // Usa a constante definida
    $stmtCfKey->bind_param("s", $chaveCfDb);
    $stmtCfKey->execute();
    $resultCfKey = $stmtCfKey->get_result();

    if ($rowCfKey = $resultCfKey->fetch_assoc()) {
        $cloudflareSecretKey = $rowCfKey['valor'];
    }
    $stmtCfKey->close();

    if (empty($cloudflareSecretKey)) {
        error_log("Chave Secreta Cloudflare não encontrada no banco para a chave: " . CLOUDFLARE_SECRET_KEY_DB);
        throw new RuntimeException("Configuração interna do servidor incompleta [CF Key].");
    }

    // 3. Enviar pedido de verificação para a Cloudflare
    $verifyUrl = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $postData = [
        'secret'   => $cloudflareSecretKey,
        'response' => $captchaToken,
        // 'remoteip' => $_SERVER['REMOTE_ADDR'] // Opcional: Enviar IP do usuário melhora a análise da Cloudflare
    ];

    $chVerify = curl_init();
    curl_setopt($chVerify, CURLOPT_URL, $verifyUrl);
    curl_setopt($chVerify, CURLOPT_POST, true);
    curl_setopt($chVerify, CURLOPT_POSTFIELDS, http_build_query($postData)); // Envia como form-encoded
    curl_setopt($chVerify, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chVerify, CURLOPT_TIMEOUT, 10); // Timeout mais curto para verificação
    curl_setopt($chVerify, CURLOPT_SSL_VERIFYPEER, true);
    $verifyResponse = curl_exec($chVerify);
    $verifyCurlError = curl_error($chVerify);
    curl_close($chVerify);

    if ($verifyCurlError) {
        error_log("Erro de cURL ao verificar CAPTCHA: " . $verifyCurlError);
        throw new RuntimeException("Erro ao comunicar com o serviço de verificação [cURL].");
    }

    $verifyResult = json_decode($verifyResponse, true);

    // 4. Verificar o resultado da Cloudflare
    if (!$verifyResult || !isset($verifyResult['success']) || $verifyResult['success'] !== true) {
        // Logar detalhes do erro se disponíveis
        if (isset($verifyResult['error-codes'])) {
            error_log("Falha na verificação Cloudflare Turnstile: " . implode(', ', $verifyResult['error-codes']));
        } else {
             error_log("Falha na verificação Cloudflare Turnstile. Resposta: " . $verifyResponse);
        }
        // Mensagem genérica para o usuário
        throw new RuntimeException("Falha na verificação de segurança (CAPTCHA). Tente novamente.");
    }

    // --- FIM VERIFICAÇÃO CAPTCHA ---
    // Se chegou aqui, o CAPTCHA é VÁLIDO! Prosseguir com a lógica original.
    // --------------------------------------------------------------------


    // 5. Buscar o Token da API dbconsultas no banco de dados local
    $tokenApi = null;
    $sqlToken = "SELECT valor FROM config WHERE chave = ?";
    $stmtToken = $conexao->prepare($sqlToken);
    // ... (código existente para buscar $tokenApi usando TOKEN_DB_KEY) ...
     if ($stmtToken === false) {
        throw new RuntimeException("Erro ao preparar consulta do token API: " . $conexao->error);
     }
     $chaveTokenDb = TOKEN_DB_KEY;
     $stmtToken->bind_param("s", $chaveTokenDb);
     $stmtToken->execute();
     $resultToken = $stmtToken->get_result();
     if ($rowToken = $resultToken->fetch_assoc()) {
        $tokenApi = $rowToken['valor'];
     }
     $stmtToken->close();

     if (empty($tokenApi)) {
        error_log("Token da API dbconsultas não encontrado no banco para chave: " . TOKEN_DB_KEY);
        throw new RuntimeException("Configuração interna do servidor incompleta [Token API].");
     }


    // 6. Preparar e fazer a chamada para a API Externa (api.dbconsultas.com)
    $externalApiUrl = "https://api.dbconsultas.com/api/v1/{$tokenApi}/datalinkcpf/{$cpfLimpo}";
    // ... (código cURL existente para chamar $externalApiUrl) ...
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
        error_log("Erro de cURL API externa: " . $curlError);
        throw new RuntimeException("Erro ao comunicar com o serviço de consulta [cURL API].");
     }

     if ($httpStatusCode >= 400) {
        $errorDataExt = json_decode($externalApiResponse, true);
        $externalMessage = isset($errorDataExt['message']) ? $errorDataExt['message'] : "Serviço de consulta retornou erro {$httpStatusCode}.";
        throw new RuntimeException($externalMessage);
     }


    // 7. Retornar a resposta da API externa para o frontend
    ob_end_clean();
    echo $externalApiResponse;
    exit();

} catch (mysqli_sql_exception $e) {
    ob_end_clean(); // Limpa buffer antes de enviar erro
    error_log("Erro de Banco de Dados (api.php): " . $e->getMessage() . " na linha " . $e->getLine());
    echo json_encode(["success" => false, "message" => "Erro interno no servidor [DB]."]);

} catch (InvalidArgumentException $e) {
    ob_end_clean();
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => $e->getMessage()]);

} catch (RuntimeException $e) {
     ob_end_clean();
     // Pode ser erro nosso ou da API externa ou da verificação CF
     http_response_code(500); // Usar 400 se for erro de CAPTCHA inválido? Talvez melhor.
     error_log("Erro de Runtime (api.php): " . $e->getMessage());
     // Se for erro de CAPTCHA, a mensagem já está definida
     echo json_encode(["success" => false, "message" => $e->getMessage()]);

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    error_log("Erro Geral (api.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno inesperado no servidor."]);

} finally {
    if (isset($conexao) && $conexao instanceof mysqli && $conexao->thread_id) {
        // $conexao->close();
    }
    // Garante que algo seja enviado se ob_end_clean foi chamado em erro e nada foi ecoado
    // ob_flush();
}
?>