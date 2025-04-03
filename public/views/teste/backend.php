<?php
if (!isset($_GET['cpf'])) {
    echo json_encode(["erro" => "CPF não informado."]);
    exit;
}

$cpf = preg_replace('/\D/', '', $_GET['cpf']); // Remove caracteres não numéricos

if (strlen($cpf) !== 11) {
    echo json_encode(["erro" => "CPF inválido."]);
    exit;
}

$url = "https://sphereapis.shop/apis/consultabigdatacompleta.php?cpf=$cpf";

// Faz a requisição na API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Caso o site tenha problemas com SSL

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(["erro" => "Erro ao consultar API. Código: $httpCode"]);
} else {
    echo $response;
}
?>
