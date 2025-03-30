<?php
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

$conexao = new mysqli($host, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Erro na conexão: " . $conexao->connect_error);
}

function getToken($conexao) {
    $sql = "SELECT valor FROM config WHERE chave = 'token_api' LIMIT 1";
    $resultado = $conexao->query($sql);
    
    if ($resultado->num_rows > 0) {
        $linha = $resultado->fetch_assoc();
        return $linha["valor"];
    } else {
        return null;
    }
}
?>
