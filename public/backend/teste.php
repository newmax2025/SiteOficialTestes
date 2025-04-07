<?php
header('Content-Type: application/json');

$cpf = '16113849660';
$token = '5fa870ba-d164-4854-ac19-600ee9f4f981';
$url = "https://consultafacil.pro/api/cpf/{$cpf}?token={$token}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);
$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo json_encode([
    "status_code" => $httpStatusCode,
    "curl_error" => $curlError,
    "response" => json_decode($response, true)
]);
