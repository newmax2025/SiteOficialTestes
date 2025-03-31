<?php
// --- Development Error Reporting ---
// IMPORTANTE: Habilita a exibição de todos os erros para depuração.
// Comente ou altere estas linhas para o ambiente de produção.
error_reporting(E_ALL);
ini_set('display_errors', 1);
// --- End Development Error Reporting ---

// Caminho para o autoload do Composer
// config.php está em 'backend/', vendor está na raiz ('TESTES/'), então subimos um nível '../'
require_once __DIR__ . '/../vendor/autoload.php'; // <-- CORRIGIDO

use Dotenv\Dotenv;

// Carrega variáveis do .env
// config.php está em 'backend/', .env está na raiz ('TESTES/'), então subimos um nível '../'
try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../'); // <-- CORRIGIDO
    $dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $e) {
    // Lança uma exceção em vez de morrer, permite que login.php trate o erro e retorne JSON
    throw new RuntimeException("Erro: Não foi possível carregar o arquivo .env. Verifique o caminho '../' a partir de config.php. Detalhes: " . $e->getMessage());
}

// Verifica se as variáveis essenciais do .env foram carregadas
if (!isset($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS'])) {
     // Lança uma exceção em vez de morrer
    throw new RuntimeException("Erro: Variáveis de banco de dados (DB_HOST, DB_NAME, DB_USER, DB_PASS) não definidas no arquivo .env ou o arquivo não foi carregado.");
}

// Configuração do banco de dados
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

// Reporta erros do MySQL como exceções para serem capturadas pelo try...catch
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexao = new mysqli($host, $username, $password, $dbname);
    $conexao->set_charset("utf8mb4");
    // REMOVIDO: echo "Banco de dados conectado com sucesso!"; // Isso quebrava a resposta JSON
} catch (mysqli_sql_exception $e) {
    // Lança uma exceção em vez de morrer
    // Loga o erro detalhado para o administrador do servidor (verifique os logs do PHP/servidor web)
    error_log("Erro de Conexão com Banco de Dados: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
    // Lança uma mensagem genérica para ser capturada por login.php
    throw new RuntimeException("Não foi possível conectar ao banco de dados. Tente novamente mais tarde.");
}

// A variável $conexao está agora disponível para scripts que incluem este arquivo (como login.php)
?>