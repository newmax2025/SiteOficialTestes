<?php
require 'vendor/autoload.php'; // Carregar dotenv
Dotenv\Dotenv::createImmutable(__DIR__)->load();

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

$conexao = new mysqli($host, $username, $password, $dbname);

if ($conexao->connect_error) {
    error_log("Erro na conexão com o banco de dados: " . $conexao->connect_error);
    echo json_encode(["error" => "Erro interno no servidor"]);
    exit;
}


function getToken($conexao) {
    $stmt = $conexao->prepare("SELECT valor FROM config WHERE chave = 'token_api' LIMIT 1");
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        return $row["valor"];
    }
    return null;
}

?>
