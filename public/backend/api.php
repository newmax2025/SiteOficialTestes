<?php
header('Content-Type: application/json');
ob_start();

// Inclui a configuração do banco de dados e outras configurações
require 'config.php';

// Defina a chave usada para buscar o token no seu banco de dados
define('TOKEN_DB_KEY', 'token_api');

define('CONTADOR_NOME_CONSULTA', 'ConsultaCPF_DBConsultas');

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

        // --- VERIFICAÇÃO DE SUCESSO E ATUALIZAÇÃO DO CONTADOR ---
    if ($httpStatusCode >= 200 && $httpStatusCode < 300) { // Sucesso (2xx)

        // -------- INÍCIO: Bloco de atualização do contador --------
        // Verifica se a conexão ainda está ativa antes de usar
        if ($conexao->ping()) {
            $nomeConsulta = CONTADOR_NOME_CONSULTA; // Usa a constante
            $sqlContador = "UPDATE contador_consultas SET numero_consultas = numero_consultas + 1 WHERE nome = ?";
            $stmtContador = $conexao->prepare($sqlContador);

            if ($stmtContador) {
                $stmtContador->bind_param("s", $nomeConsulta);
                if (!$stmtContador->execute()) {
                    // Logar erro, mas não impedir a resposta principal
                    error_log("Falha ao atualizar contador (" . $nomeConsulta . "): " . $stmtContador->error);
                }
                $stmtContador->close(); // Fecha o statement do contador
            } else {
                error_log("Falha ao preparar statement do contador: " . $conexao->error);
            }
        } else {
            error_log("Conexão com BD perdida antes de atualizar contador.");
            // Considerar se isso é um erro crítico para sua aplicação
        }
        // -------- FIM: Bloco de atualização do contador --------

        // Limpa qualquer saída anterior e envia a resposta da API externa
        ob_end_clean();
        echo $externalApiResponse;
        exit(); // Termina a execução aqui após sucesso

    } else {
        // A API externa retornou um erro (4xx ou 5xx)
        $errorData = json_decode($externalApiResponse, true);
        $externalMessage = $errorData['message'] ?? "Serviço de consulta retornou erro {$httpStatusCode}.";
        // Lança exceção para ser pega pelo bloco catch apropriado
        throw new RuntimeException($externalMessage);
    }

} catch (mysqli_sql_exception $e) {
    ob_end_clean(); // Limpa o buffer em caso de erro
    http_response_code(500); // Internal Server Error
    error_log("Erro de Banco de Dados (api.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno no servidor [DB]."]);

} catch (InvalidArgumentException $e) {
    ob_end_clean(); // Limpa o buffer
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => $e->getMessage()]);

} catch (RuntimeException $e) {
    ob_end_clean(); // Limpa o buffer
    // O código de status pode variar dependendo do erro (500, 502, 404, etc.)
    // Mantendo 500 como padrão para erros de runtime não tratados especificamente
    if ($httpStatusCode >= 400) {
         http_response_code($httpStatusCode); // Usa o status da API externa se disponível
    } else {
         http_response_code(500); // Internal Server Error
    }
    error_log("Erro de Runtime (api.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => $e->getMessage()]);

} catch (Exception $e) {
    ob_end_clean(); // Limpa o buffer
    http_response_code(500); // Internal Server Error
    error_log("Erro Geral (api.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno inesperado no servidor."]);

} finally {
    // Garante que a conexão principal seja fechada se foi aberta
    if (isset($conexao) && $conexao instanceof mysqli) {
        $conexao->close();
    }
    // Garante que o buffer de saída seja limpo se ainda existir
    if (ob_get_level() > 0) {
       ob_end_flush(); // ou ob_end_clean() se não quiser enviar nada que possa ter ficado no buffer
    }
}
?>