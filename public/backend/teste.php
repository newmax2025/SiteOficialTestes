<?php
header('Content-Type: application/json');

$cpf = '16113849660';
$token = '5fa870ba-d164-4854-ac19-600ee9f4f981';
$url = "https://consultafacil.pro/api/cpf/{$cpf}?token={$token}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // temporariamente false pra testar
curl_setopt($ch, CURLOPT_HEADER, true); // captura cabeçalhos
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0'); // força User-Agent

$response = curl_exec($ch);
$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
$curlError = curl_error($ch);
curl_close($ch);

echo json_encode([
    "status_code" => $httpStatusCode,
    "curl_error" => $curlError,
    "headers" => $headers,
    "body_raw" => $body,
    "body_json" => json_decode($body, true)
]);
