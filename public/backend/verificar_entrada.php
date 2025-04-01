<?php
// verificar_entrada.php
header('Content-Type: application/json');
ob_start(); // Inicia o buffer de saída

require 'config.php'; // Sua configuração de BD
require 'includes/verificador_turnstile.php'; // Onde está a função verificarTokenTurnstile

$conexao = null; // Inicializa a variável de conexão

try {
    // Estabelece conexão com o BD (adapte conforme sua config.php)
    // Exemplo: $conexao = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    // Verifique erros de conexão: if ($conexao->connect_error) { ... }
    // Assumindo que sua config.php já cria a variável $conexao globalmente ou a retorna

    if (!$conexao || !($conexao instanceof mysqli)) {
         // Log interno detalhado
         error_log("Falha ao obter instância válida de conexão com o banco de dados em verificar_entrada.php.");
         // Resposta genérica
         throw new RuntimeException("Erro interno de configuração do servidor [DB Connect].");
    }

    // 1. Receber o token Turnstile do POST
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($inputData['cf-turnstile-response'])) {
        http_response_code(400); // Bad Request
        throw new InvalidArgumentException("Token de verificação ausente ou inválido.");
    }
    $turnstileToken = trim($inputData['cf-turnstile-response']);

    // 2. Obter o IP do cliente (recomendado)
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? null;

    // 3. Chamar a função de verificação
    $isCaptchaValid = verificarTokenTurnstile($conexao, $turnstileToken, $clientIp);

    // 4. Retornar o resultado
    if ($isCaptchaValid) {
        ob_end_clean(); // Limpa o buffer antes de enviar a resposta
        http_response_code(200); // OK
        echo json_encode([
            "success" => true,
            "message" => "Verificação bem-sucedida."
        ]);
        exit();
    } else {
        ob_end_clean(); // Limpa o buffer
        http_response_code(403); // Forbidden
        // A função verificarTokenTurnstile já logou os detalhes do erro
        echo json_encode([
            "success" => false,
            "message" => "Falha na verificação de segurança (CAPTCHA)."
        ]);
        exit();
    }

} catch (InvalidArgumentException $e) { // Erro nos dados recebidos (400)
    if (ob_get_level() > 0) ob_end_clean();
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);

} catch (RuntimeException $e) { // Erros de configuração ou comunicação (500 ou outros definidos)
    if (ob_get_level() > 0) ob_end_clean();
    // Mantém o código HTTP se já foi definido (ex: 403 da falha de captcha)
    if (http_response_code() < 400) {
        http_response_code(500); // Internal Server Error
    }
    // Logar o erro Runtime aqui também é uma boa prática
    error_log("Erro de Runtime (verificar_entrada.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => $e->getMessage()]);

} catch (Exception $e) { // Outros erros inesperados (500)
    if (ob_get_level() > 0) ob_end_clean();
    http_response_code(500);
    error_log("Erro Geral Inesperado (verificar_entrada.php): " . $e->getMessage() . "\n" . $e->getTraceAsString());
    echo json_encode(["success" => false, "message" => "Erro interno inesperado no servidor."]);

} finally {
     // Garante que a conexão seja fechada
     if (isset($conexao) && $conexao instanceof mysqli && $conexao->thread_id) {
        $conexao->close();
    }
     // Garante que qualquer saída restante no buffer seja limpa se ainda não foi
     if (ob_get_level() > 0) {
        ob_end_clean();
     }
}
?>