<?php
require __DIR__ . '/../vendor/autoload.php'; // Garante que está buscando no diretório correto

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); // Ajuste o caminho se necessário
$dotenv->load();

// Verificação para garantir que o .env foi carregado corretamente
if (!isset($_ENV['DB_HOST'])) {
    die("Erro: Arquivo .env não carregado corretamente.");
}

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexao = new mysqli($host, $username, $password, $dbname);
    $conexao->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

function getToken($conexao) {
    $sql = "SELECT valor FROM config WHERE chave = ?";
    $stmt = $conexao->prepare($sql);
    $chave = 'token_api';
    $stmt->bind_param("s", $chave);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $linha = $resultado->fetch_assoc();
        return $linha["valor"];
    }
    return null;
}
?>
