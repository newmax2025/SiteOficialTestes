<?php
/**
 * Verificador Cloudflare Turnstile
 *
 * Este arquivo contém a função para verificar um token do Cloudflare Turnstile
 * usando a chave secreta armazenada no banco de dados.
 */

// Defina a chave usada para buscar a Chave Secreta do Cloudflare no banco de dados
define('CLOUDFLARE_SECRET_KEY_DB', 'cloudflare_secret_key'); // Certifique-se que esta chave existe na sua tabela 'config'

// Defina a URL de verificação do Cloudflare Turnstile
define('CLOUDFLARE_VERIFY_URL', 'https://challenges.cloudflare.com/turnstile/v0/siteverify');

/**
 * Verifica um token do Cloudflare Turnstile.
 *
 * Entra em contato com a API do Cloudflare para validar o token fornecido pelo cliente.
 * Requer a chave secreta do Turnstile configurada no banco de dados.
 *
 * @param mysqli $conexao Uma instância ativa da conexão MySQLi.
 * @param string $turnstileToken O token recebido do cliente (normalmente de 'cf-turnstile-response').
 * @param string|null $remoteIp O endereço IP do cliente final (opcional, mas recomendado para segurança).
 *
 * @return bool Retorna true se o token for válido, false caso contrário.
 *
 * @throws RuntimeException Lança exceção se a chave secreta não for encontrada no BD
 * ou se ocorrer um erro de comunicação com a API do Cloudflare.
 * @throws InvalidArgumentException Se o token fornecido estiver vazio.
 */
function verificarTokenTurnstile(mysqli $conexao, string $turnstileToken, ?string $remoteIp = null): bool
{
    if (empty($turnstileToken)) {
        throw new InvalidArgumentException("Token do Turnstile não pode ser vazio.");
    }

    // --- 1. Buscar a Chave Secreta do Cloudflare no Banco de Dados ---
    $secretKey = null;
    $sqlSecret = "SELECT valor FROM config WHERE chave = ?";
    $stmtSecret = $conexao->prepare($sqlSecret);

    if ($stmtSecret === false) {
        // Log do erro real para depuração interna
        error_log("Erro ao preparar consulta da chave secreta Cloudflare: " . $conexao->error);
        // Mensagem genérica para o usuário ou chamador da função
        throw new RuntimeException("Erro ao acessar configuração de segurança [DB Prepare].");
    }

    $dbKey = CLOUDFLARE_SECRET_KEY_DB;
    $stmtSecret->bind_param("s", $dbKey);
    $stmtSecret->execute();
    $resultSecret = $stmtSecret->get_result();

    if ($rowSecret = $resultSecret->fetch_assoc()) {
        $secretKey = $rowSecret['valor'];
    }
    $stmtSecret->close();

    if (empty($secretKey)) {
        error_log("Chave Secreta do Cloudflare não encontrada no banco de dados para a chave: " . CLOUDFLARE_SECRET_KEY_DB);
        // É um erro crítico de configuração, lança exceção
        throw new RuntimeException("Configuração de segurança interna incompleta [CF Secret Missing].");
    }

    // --- 2. Preparar dados para a API do Cloudflare ---
    $postData = [
        'secret'   => $secretKey,
        'response' => $turnstileToken,
    ];
    // Adiciona o IP do cliente se fornecido (recomendado)
    if ($remoteIp) {
        $postData['remoteip'] = $remoteIp;
    }

    // --- 3. Fazer a chamada cURL para o Cloudflare ---
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, CLOUDFLARE_VERIFY_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    // Envia como application/x-www-form-urlencoded (padrão para http_build_query)
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout de 10 segundos para a verificação
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Timeout de 5 segundos para conectar
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Sempre verificar o certificado SSL

    $responseBody = curl_exec($ch);
    $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // --- 4. Processar a Resposta ---
    if ($curlError) {
        error_log("Erro de cURL ao verificar Turnstile (" . CLOUDFLARE_VERIFY_URL . "): " . $curlError);
        // Erro de comunicação, lança exceção
        throw new RuntimeException("Erro ao comunicar com o serviço de verificação CAPTCHA [cURL Error].");
    }

    if ($httpStatusCode !== 200) {
         error_log("Cloudflare Turnstile API (" . CLOUDFLARE_VERIFY_URL . ") retornou status HTTP: " . $httpStatusCode . ". Body: " . $responseBody);
        // Erro na API do Cloudflare, lança exceção
        throw new RuntimeException("Serviço de verificação CAPTCHA retornou erro inesperado [HTTP Status: " . $httpStatusCode . "].");
    }

    $responseData = json_decode($responseBody, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
         error_log("Erro ao decodificar resposta JSON do Cloudflare Turnstile (" . CLOUDFLARE_VERIFY_URL . "). Body: " . $responseBody);
         // Resposta inválida, lança exceção
         throw new RuntimeException("Resposta inválida do serviço de verificação CAPTCHA [JSON Decode Error].");
    }

    // --- 5. Verificar o Sucesso e Retornar ---
    if (isset($responseData['success']) && $responseData['success'] === true) {
        // Verificação bem-sucedida!
        return true;
    } else {
        // Verificação falhou. Logar os códigos de erro se existirem.
        $errorCodes = isset($responseData['error-codes']) && is_array($responseData['error-codes'])
                      ? implode(', ', $responseData['error-codes'])
                      : 'N/A';
        error_log("Falha na verificação Turnstile. Códigos de erro: [" . $errorCodes . "]. Resposta completa: " . $responseBody);
        // Retorna false indicando que a *verificação* falhou (não necessariamente um erro de sistema)
        return false;
    }
}

?>