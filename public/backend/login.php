<?php
// Inicia a sessão ANTES de qualquer saída
session_start();

// --- Development Error Reporting ---
// IMPORTANTE: Habilita a exibição de todos os erros para depuração.
// Comente ou altere estas linhas para o ambiente de produção.
error_reporting(E_ALL);
ini_set('display_errors', 1);
// --- End Development Error Reporting ---

// Define o cabeçalho ANTES de qualquer saída. Garante que a resposta será JSON.
header('Content-Type: application/json');

// Inicia o buffer de saída para capturar saídas inesperadas (embora o try/catch ajude)
ob_start();

try {
    // Inclui a configuração do banco de dados
    // config.php está no mesmo diretório (backend/)
    require_once __DIR__ . '/config.php'; // <-- CORRIGIDO e mais robusto

    // Recebe os dados do POST (enviados como JSON pelo fetch)
    $inputData = json_decode(file_get_contents("php://input"), true);

    // Valida se os dados foram recebidos e decodificados corretamente
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new InvalidArgumentException("Erro ao decodificar os dados JSON recebidos.");
    }

    if (!isset($inputData["username"], $inputData["password"])) {
        // Envia resposta de erro em JSON e termina o script
        echo json_encode(["success" => false, "message" => "Nome de usuário e senha são obrigatórios."]);
        exit();
    }

    $user = trim($inputData["username"]);
    $pass = trim($inputData["password"]);

    // Função interna para verificar login (passando a conexão como parâmetro)
    function verificarLogin($conexaoDb, $usuario, $senhaInput, $tabela, $chaveSessao, $urlRedirecionamento) {
        // Usa prepared statements para segurança contra SQL Injection
        $sql = "SELECT senha FROM `$tabela` WHERE usuario = ?"; // Usar crases em nomes de tabela/coluna se necessário
        $stmt = $conexaoDb->prepare($sql);
        if (!$stmt) {
             // Lança exceção se a preparação da query falhar
             throw new RuntimeException("Erro ao preparar a consulta SQL: " . $conexaoDb->error);
        }
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $hashSenhaDB = $row["senha"];

            // Verifica se a senha fornecida corresponde ao hash no banco
            if (password_verify($senhaInput, $hashSenhaDB)) {
                // Login bem-sucedido
                $_SESSION[$chaveSessao] = $usuario; // Define a variável de sessão
                $stmt->close();
                // Retorna os dados para o JSON de sucesso
                return ["success" => true, "redirect" => $urlRedirecionamento];
            }
        }
        $stmt->close();
        // Retorna null se não encontrou ou senha inválida nesta tabela
        return null;
    }

    // Tenta logar como cliente
    $loginResult = verificarLogin($conexao, $user, $pass, "clientes", "usuario", "aM.html"); // Assumindo que aM.html está na raiz ou views/
    if ($loginResult) {
        echo json_encode($loginResult);
        exit();
    }

    // Tenta logar como admin
    $loginResult = verificarLogin($conexao, $user, $pass, "admin", "admin", "admin.html"); // Assumindo que admin.html está na raiz ou views/
    if ($loginResult) {
        echo json_encode($loginResult);
        exit();
    }

    // Se chegou aqui, não logou em nenhuma tabela
    echo json_encode(["success" => false, "message" => "Usuário ou senha inválidos."]);
    exit();

} catch (InvalidArgumentException $e) {
    // Erro nos dados recebidos
    http_response_code(400); // Bad Request
    echo json_encode(["success" => false, "message" => "Erro nos dados enviados: " . $e->getMessage()]);

} catch (mysqli_sql_exception $e) {
    // Erro específico do banco de dados durante a execução da query em verificarLogin
    error_log("Erro SQL em login.php: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
    http_response_code(500); // Internal Server Error
    echo json_encode(["success" => false, "message" => "Erro ao processar sua solicitação. Tente novamente."]); // Mensagem genérica para o usuário

} catch (RuntimeException $e) {
    // Erro geral capturado (pode ser do config.php ou outros)
    error_log("Erro Runtime em login.php: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
    http_response_code(500); // Internal Server Error
    echo json_encode(["success" => false, "message" => "Erro interno do servidor: " . $e->getMessage()]); // Mostra a mensagem da exceção

} catch (Throwable $e) { // Captura qualquer outro erro/exceção não previsto
    error_log("Erro inesperado em login.php: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
    http_response_code(500); // Internal Server Error
    echo json_encode(["success" => false, "message" => "Ocorreu um erro inesperado."]);

} finally {
    // Limpa o buffer de saída, caso algo tenha sido escrito inesperadamente antes do JSON
    ob_end_flush(); // Ou ob_end_clean() se não quiser enviar nada do buffer em caso de erro grave
}
?>