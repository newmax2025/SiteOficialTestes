<?php
session_start();
header('Content-Type: application/json');

// Verifica autenticação
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado.']);
    exit;
}

// Verifica se recebeu CPF via POST
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['cpf'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'CPF não informado.']);
    exit;
}

$cpf = preg_replace('/\D/', '', $input['cpf']); // Limpa CPF
if (strlen($cpf) !== 11) {
    http_response_code(400);
    echo json_encode(['erro' => 'CPF inválido.']);
    exit;
}

// Token da API (buscado do banco ou fixo temporariamente)
$token = '5fa870ba-d164-4854-ac19-600ee9f4f981'; // substitua por seu método seguro

$url = "https://consultafacil.pro/api/cpf/{$cpf}?token={$token}";

// Consulta à API externa
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false, // ajuste conforme seu servidor
    CURLOPT_USERAGENT => 'Mozilla/5.0',
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Valida resposta da API
if ($curlError) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro na requisição: ' . $curlError]);
    exit;
}

if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode(['erro' => 'Erro na consulta externa.', 'codigo_http' => $httpCode]);
    exit;
}

// Tenta decodificar JSON
$data = json_decode($response, true);
if ($data === null) {
    http_response_code(500);
    echo json_encode(['erro' => 'Resposta da API inválida.']);
    exit;
}

// Aqui você pode salvar no banco se quiser (logs etc.)

// Envia resultado para o JavaScript
echo json_encode(['sucesso' => true, 'dados' => $data]);
