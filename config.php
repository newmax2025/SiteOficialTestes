<?php
$host = "mysql.hostinger.com";
$dbname = "u377990636_DataBase";
$username = "u377990636_Admin";
$password = "+c4Nrz@H5";

$conexao = new mysqli($host, $username, $password, $dbname);

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