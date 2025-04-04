<?php
require __DIR__ . '/../../vendor/autoload.php'; // Garante que está buscando no diretório correto

use Dotenv\Dotenv;

// --- Carregar variáveis do .env com melhor tratamento de erro ---
try {
    // __DIR__ aponta para o diretório onde config.php está.
    // Ajuste '/../' se o .env estiver em um local diferente em relação a config.php
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $e) {
    // Erro se o diretório/arquivo .env não for encontrado
    error_log("Erro Crítico: Arquivo .env não encontrado ou caminho inválido. Detalhes: " . $e->getMessage());
    die("Erro interno na configuração do servidor [Env Path].");
} catch (\Dotenv\Exception\InvalidFileException $e) {
    // Erro se o .env tiver sintaxe inválida
    error_log("Erro Crítico: Arquivo .env possui formato inválido. Detalhes: " . $e->getMessage());
     die("Erro interno na configuração do servidor [Env Format].");
}

// --- Verificar se variáveis essenciais foram carregadas ---
if (!isset($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS'])) {
    error_log("Erro Crítico: Variáveis de banco de dados (DB_HOST, DB_NAME, DB_USER, DB_PASS) não definidas no .env.");
    die("Erro interno na configuração do servidor [Env Vars].");
}

// --- Configuração do banco de dados ---
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

// Ativa a reportagem de erros do MySQLi como exceções
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // --- Estabelece a conexão ---
    $conexao = new mysqli($host, $username, $password, $dbname);

    // --- Define o Charset (essencial, manter) ---
    // É melhor verificar o retorno explicitamente do que confiar apenas na exceção
    if (!$conexao->set_charset("utf8mb4")) {
        // Logar o erro é importante, mas talvez não precise parar tudo
        error_log("ALERTA: Falha ao definir charset utf8mb4: " . $conexao->errno . " - " . $conexao->error);
        // Dependendo da criticidade, você pode lançar uma exceção aqui:
        // throw new Exception("Não foi possível definir o charset da conexão.");
    }

    // --- Define o Timezone da Conexão (com verificação explícita) ---
    $timezone = 'America/Sao_Paulo'; // Ou o timezone correto
    $timezoneQuery = "SET time_zone = '" . $conexao->real_escape_string($timezone) . "'"; // Escapar por segurança, embora improvável aqui

    if (!$conexao->query($timezoneQuery)) {
        // A query falhou! Logar o erro específico, mas NÃO necessariamente parar o script.
        error_log("ALERTA: Falha ao executar '{$timezoneQuery}'. Erro MySQL [" . $conexao->errno . "]: " . $conexao->error . ". Verifique se o timezone '{$timezone}' é válido/carregado no servidor MySQL e se o usuário '{$username}' tem permissão.");

    } // Se a query funcionou, não faz nada aqui, continua normal.

    // Se chegou aqui, a conexão está pronta para ser usada.

} catch (mysqli_sql_exception $e) {
    // Captura erros de CONEXÃO ou outros erros MySQLi que viraram exceção ANTES do set_charset/time_zone ou se eles lançarem exceção
    error_log("Erro CRÍTICO na conexão/configuração inicial com o banco de dados: " . $e->getMessage() . " (Código: " . $e->getCode() . ")");
    // Mensagem genérica para o usuário final
    die("Erro interno no servidor [Database Connection]. Por favor, tente novamente mais tarde.");
} catch (Exception $e) {
    // Captura outras exceções gerais que podem ter sido lançadas (ex: do charset ou timezone se você descomentar o throw)
     error_log("Erro CRÍTICO na configuração: " . $e->getMessage());
     die("Erro interno no servidor [Configuration].");
}

// NÃO feche a conexão $conexao->close(); aqui.
// Ela precisa ficar aberta para ser usada pelos scripts que incluem este config.php.
?>