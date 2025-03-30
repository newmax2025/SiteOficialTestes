<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");


require 'config.php';
$conexao = new mysqli($host, $username, $password, $dbname);


$token = getToken($conexao);

if (!$token) {
    echo json_encode(["error" => "Token não encontrado"]);
    exit;
}

if (!isset($_GET['cpf'])) {
    echo json_encode(["error" => "CPF não informado"]);
    exit;
}

$cpf = preg_replace('/\D/', '', $_GET['cpf']);

if (strlen($cpf) !== 11) {
    echo json_encode(["error" => "CPF inválido"]);
    exit;
}

// Monta a URL da API usando o token do banco de dados
$url = "https://api.dbconsultas.com/api/v1/" . $token . "/datalinkcpf/" . $cpf;

$response = file_get_contents($url);

if ($response === false) {
    echo json_encode(["error" => "Erro ao consultar API"]);
} else {
    echo $response;
}
?>
