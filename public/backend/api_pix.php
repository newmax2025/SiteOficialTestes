<?php
session_start();
header('Content-Type: application/json');

// Verifica autenticação
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado.']);
    exit;
}

// Inclui a configuração do banco de dados
require 'config.php';

// Lê os dados JSON enviados
$input = json_decode(file_get_contents('php://input'), true);

// Valida CPF
if (!isset($input['cpf']) || empty($input['cpf'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'CPF não informado.']);
    exit;
}
$cpf = preg_replace('/\D/', '', $input['cpf']);

// Valida Nome
if (!isset($input['nome']) || empty(trim($input['nome']))) {
    http_response_code(400);
    echo json_encode(['erro' => 'Nome não informado.']);
    exit;
}
$nome = trim($input['nome']); // mantém os espaços intactos

// Busca o token no banco de dados
$token = null;
$stmt = $conexao->prepare("SELECT valor FROM config WHERE chave = ?");
$chave = 'token_nova_api';
$stmt->bind_param("s", $chave);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $token = $row['valor'];
}
$stmt->close();

if (empty($token)) {
    http_response_code(500);
    echo json_encode(['erro' => 'Token da API não encontrado no banco de dados.']);
    exit;
}

// Monta a URL com nome com espaços
$url = "https://consultafacil.pro/api/consult-pix/" . urlencode($nome) . "/$cpf?token=$token";


// Executa a requisição externa
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0',
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

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

// Decodifica a resposta da API externa
$data = json_decode($response, true);
if (!isset($data['dados'])) {
    http_response_code(500);
    echo json_encode(['erro' => 'Resposta inesperada da API externa.']);
    exit;
}

// Retorna apenas os dados
echo json_encode(['dados' => $data['dados']]);

?>
