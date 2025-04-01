<?php
require 'chave_turnstile.php'; // Inclui apenas a lógica da chave sem afetar outras funções

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';

    if (!$token) {
        echo json_encode(["success" => false, "error" => "Token ausente."]);
        exit;
    }

    $url = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
    $data = [
        "secret" => $cloudflare_secret, 
        "response" => $token
    ];

    $options = [
        "http" => [
            "header" => "Content-Type: application/x-www-form-urlencoded\r\n",
            "method" => "POST",
            "content" => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result, true);

    echo json_encode(["success" => $response["success"]]);
}
?>
