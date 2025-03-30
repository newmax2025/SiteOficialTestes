<?php
require 'vendor/autoload.php'; // Carregar dotenv
Dotenv\Dotenv::createImmutable(__DIR__)->load();

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

$conexao = new mysqli($host, $username, $password, $dbname);

if ($conexao->connect_error) {
    die(json_encode(["error" => "Erro na conexão com o banco de dados"]));
}

function getToken($conexao) {
    $stmt = $conexao->prepare("SELECT valor FROM config WHERE chave = 'token_api' LIMIT 1");
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        return $resultado->fetch_assoc()["valor"];
    } else {
        return null;
    }
}
?>
