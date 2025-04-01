<?php
session_start();
header('Content-Type: application/json');
ob_start();
error_reporting(0);

require 'config.php';
require 'chave_turnstile.php'; // Obtém a chave secreta do banco

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["username"], $data["password"], $data["captchaResponse"])) {
    echo json_encode(["success" => false, "message" => "Preencha todos os campos!"]);
    exit();
}

// Verifica o CAPTCHA primeiro
$captchaResponse = $data["captchaResponse"];
$captchaUrl = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
$captchaData = [
    "secret" => $cloudflare_secret,
    "response" => $captchaResponse
];

$options = [
    "http" => [
        "header" => "Content-Type: application/x-www-form-urlencoded\r\n",
        "method" => "POST",
        "content" => http_build_query($captchaData)
    ]
];

$context = stream_context_create($options);
$captchaResult = file_get_contents($captchaUrl, false, $context);
$captchaValidation = json_decode($captchaResult, true);

if (!$captchaValidation["success"]) {
    echo json_encode(["success" => false, "message" => "Falha na verificação do CAPTCHA."]);
    exit();
}

// Se passou no CAPTCHA, verifica o login
$user = trim($data["username"]);
$pass = trim($data["password"]);

if (verificarLogin($conexao, $user, $pass, "clientes", "usuario", "aM.html")) {
    exit();
}

if (verificarLogin($conexao, $user, $pass, "admin", "admin", "admin.html")) {
    exit();
}

echo json_encode(["success" => false, "message" => "Usuário ou senha inválidos"]);
exit();

// Função para verificar login
function verificarLogin($conexao, $user, $pass, $tabela, $sessao, $redirect) {
    $sql = "SELECT senha FROM $tabela WHERE usuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if (password_verify($pass, $row["senha"])) {
            $_SESSION[$sessao] = $user;
            echo json_encode(["success" => true, "redirect" => $redirect]);
            return true;
        }
    }
    return false;
}
?>
