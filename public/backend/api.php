<?php
date_default_timezone_set('America/Sao_Paulo');
// **IMPORTANTE: Garanta que a sessão seja iniciada ANTES de qualquer output**
// Coloque isso no topo do seu script ou em um 'require' inicial
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');
// Inicia o buffer de saída para controlar o output, especialmente em caso de erros
ob_start();

// Inclui o arquivo de configuração (assume que estabelece a conexão $conexao com o BD)
require 'config.php';

// --- Constantes ---
// Chave usada para buscar o token da API no banco de dados (tabela 'config')
define('TOKEN_DB_KEY', 'token_nova_api');
// Nome/Tipo desta consulta para ser registrado na tabela 'log_consultas'
define('TIPO_CONSULTA_ATUAL', 'ConsultaCPF_DBConsultas'); // <-- Ajuste se desejar outro nome no log

try {
    // --- Obtenção do ID do Cliente da Sessão ---
    // **CRÍTICO: Verifique qual chave ('user_id', 'cliente_id', etc.) seu sistema de login
    // usa para armazenar o ID do cliente na sessão e ajuste abaixo!**
    $idClienteLog = $_SESSION['usuario_id'] ?? null;

    // **Verificação Opcional (Recomendado):**
    // Se a consulta SÓ pode ser feita por clientes logados, descomente para lançar erro 401.
    // if ($idClienteLog === null) {
    //     throw new RuntimeException("Acesso não autorizado. Faça login para consultar.", 401); // 401 Unauthorized
    // }
    // Se permitir log NULL mesmo sem cliente, pode deixar como está ou só logar um aviso:
    if ($idClienteLog === null) {
        error_log("Aviso: Consulta CPF realizada sem id_cliente identificado na sessão.");
    }
    // --- Fim da Obtenção do ID do Cliente ---


    // --- 1. Receber e Validar o CPF do Frontend ---
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new InvalidArgumentException("Erro ao decodificar JSON recebido.");
    }

    if (!isset($inputData['cpf']) || empty(trim($inputData['cpf']))) {
        throw new InvalidArgumentException("CPF não informado.");
    }

    // Limpa e valida o formato do CPF
    $cpfLimpo = preg_replace('/\D/', '', trim($inputData['cpf']));
    if (strlen($cpfLimpo) !== 11) {
        throw new InvalidArgumentException("Formato de CPF inválido.");
    }

    // Verifica se a conexão com o BD foi estabelecida em config.php
    if (!isset($conexao) || !$conexao instanceof mysqli) {
         throw new RuntimeException("Falha interna: Conexão com o banco de dados não estabelecida.");
    }


    // --- 2. Buscar o Token da API Externa no Banco de Dados Local ---
    $token = null;
    $sqlToken = "SELECT valor FROM config WHERE chave = ?";
    $stmtToken = $conexao->prepare($sqlToken);
    if ($stmtToken === false) {
        // Não exponha $conexao->error diretamente para o usuário em produção
        error_log("Erro ao preparar consulta do token: " . $conexao->error);
        throw new RuntimeException("Erro ao preparar consulta interna [Token].");
    }
    $chaveToken = TOKEN_DB_KEY;
    $stmtToken->bind_param("s", $chaveToken);
    if (!$stmtToken->execute()) {
        error_log("Erro ao executar consulta do token: " . $stmtToken->error);
        $stmtToken->close();
        throw new RuntimeException("Erro ao executar consulta interna [Token].");
    }
    $resultToken = $stmtToken->get_result();

    if ($rowToken = $resultToken->fetch_assoc()) {
        $token = $rowToken['valor'];
    }
    $stmtToken->close(); // Fecha o statement do token

    if (empty($token)) {
        error_log("Token da API não encontrado no banco de dados para a chave: " . TOKEN_DB_KEY);
        throw new RuntimeException("Configuração interna do servidor incompleta [Token].");
    }


    // --- 3. Chamar a API Externa (api.dbconsultas.com) usando cURL ---
    //$externalApiUrl = "https://api.dbconsultas.com/api/v1/{$token}/datalinkcpf/{$cpfLimpo}";
    $externalApiUrl = "https://consultafacil.pro/api/cpf/{$cpfLimpo}?token={$token}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $externalApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retorna a resposta como string
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);         // Tempo máximo para a requisição
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // IMPORTANTE: Mantenha true em produção

    $externalApiResponse = curl_exec($ch);
    $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Verifica erros na execução do cURL
    if ($curlError) {
        error_log("Erro de cURL ao chamar API externa: " . $curlError);
        throw new RuntimeException("Erro ao comunicar com o serviço de consulta externa [cURL].");
    }


    // --- 4. Processar Resposta e Inserir no Log se Sucesso ---
    // --- VERIFICAÇÃO DE SUCESSO E **INSERÇÃO NO LOG (COM DATETIME PHP)** ---
if ($httpStatusCode >= 200 && $httpStatusCode < 300) { // Sucesso (2xx)

    // -------- INÍCIO: Bloco de inserção no log (MODIFICADO) --------
    if (isset($conexao) && $conexao instanceof mysqli && $conexao->ping()) {
        $tipoConsultaLog = TIPO_CONSULTA_ATUAL;
        $idClienteLog = $_SESSION['usuario_id'] ?? null; // Use a chave correta da sessão

        // **NOVO: Gerar data/hora atual no PHP usando o timezone definido**
        $dataHoraAtualPHP = date('Y-m-d H:i:s'); // Formato padrão MySQL DATETIME

        // **NOVO: SQL inclui a coluna data_hora_consulta**
        $sqlLog = "INSERT INTO log_consultas (tipo_consulta, id_cliente, data_hora_consulta) VALUES (?, ?, ?)";
        $stmtLog = $conexao->prepare($sqlLog);

        if ($stmtLog) {
            // **NOVO: Bind parameters: s=tipo, i=id_cliente, s=datahora**
            $stmtLog->bind_param("sis", $tipoConsultaLog, $idClienteLog, $dataHoraAtualPHP);
            if (!$stmtLog->execute()) {
                error_log("Falha ao inserir no log (" . $tipoConsultaLog . "): " . $stmtLog->error);
            }
            $stmtLog->close();
        } else {
            error_log("Falha ao preparar statement do log: " . $conexao->error);
        }
    } else {
        error_log("Conexão com BD perdida antes de inserir no log.");
    }
    // -------- FIM: Bloco de inserção no log --------

    ob_end_clean();
    echo $externalApiResponse;
    exit();

    } else {
    // ... (Tratamento de erro da API externa - permanece igual) ...
        $errorData = json_decode($externalApiResponse, true);
        $externalMessage = $errorData['message'] ?? "Serviço de consulta retornou erro {$httpStatusCode}.";
        throw new RuntimeException($externalMessage);
    }

} catch (mysqli_sql_exception $e) {
    // Erro específico do MySQLi (conexão, query, etc.)
    ob_end_clean(); // Limpa o buffer em caso de erro
    http_response_code(500); // Internal Server Error
    error_log("Erro de Banco de Dados (api.php): " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Erro interno no servidor [DB]. Por favor, tente novamente mais tarde."]);

} catch (InvalidArgumentException $e) {
    // Erro nos dados de entrada (CPF inválido, JSON malformado)
    ob_end_clean();
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => $e->getMessage()]);

} catch (RuntimeException $e) {
    // Erros gerais durante a execução (falha no cURL, token não encontrado, erro da API externa)
    ob_end_clean();
    // Usa o código da exceção se definido (pode ser o HTTP status da API externa ou 401 do login)
    $responseCode = ($e->getCode() >= 400) ? $e->getCode() : 500; // Default para 500 se não for erro HTTP >= 400
    http_response_code($responseCode);
    error_log("Erro de Runtime (api.php): [" . $e->getCode() . "] " . $e->getMessage());
    // Mensagem mais genérica para o usuário em caso de erro 500
    $userMessage = ($responseCode == 500) ? "Ocorreu um erro inesperado no servidor." : $e->getMessage();
    echo json_encode(["success" => false, "message" => $userMessage]);

} catch (Exception $e) {
    // Captura qualquer outra exceção não prevista
    ob_end_clean();
    http_response_code(500); // Internal Server Error
    error_log("Erro Geral Inesperado (api.php): " . $e->getMessage() . "\nStack Trace: " . $e->getTraceAsString());
    echo json_encode(["success" => false, "message" => "Erro interno crítico inesperado no servidor."]);

} finally {
    // Este bloco SEMPRE será executado, ocorrendo erro ou não.
    // Garante que a conexão principal com o banco de dados seja fechada se foi aberta.
    if (isset($conexao) && $conexao instanceof mysqli) {
        $conexao->close();
    }
    // Garante que o buffer de saída seja limpo e desativado se ainda existir
    // (útil se um erro ocorreu antes de ob_end_clean() ser chamado)
    if (ob_get_level() > 0) {
       ob_end_flush(); // Envia qualquer conteúdo restante (se houver) ou ob_end_clean() para descartar.
    }
}

// Se o script chegar aqui por algum motivo inesperado (não deveria devido aos exit() e exceções),
// garante uma resposta JSON de erro padrão.
if (headers_sent() === false) {
     // Verifica se headers já foram enviados para evitar erro
     if (http_response_code() < 400) {
        // Se nenhum erro foi definido, define um erro genérico
         http_response_code(500);
     }
     echo json_encode(["success" => false, "message" => "Fim inesperado do processamento."]);
}

?>