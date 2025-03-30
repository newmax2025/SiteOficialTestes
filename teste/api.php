<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'config.php';

try {
    $token = getToken($conexao);

    if (!$token) {
        throw new Exception("Erro interno");
    }

    if (!isset($_GET['cpf'])) {
        throw new Exception("CPF não informado");
    }

    $cpf = preg_replace('/\D/', '', $_GET['cpf']);

    if (strlen($cpf) !== 11) {
        throw new Exception("CPF inválido");
    }

    // Monta a URL da API usando o token armazenado no banco
    $url = "https://api.dbconsultas.com/api/v1/$token/datalinkcpf/$cpf";

    // Inicializa cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) {
        throw new Exception("Erro ao consultar API");
    }

    echo $response;

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
