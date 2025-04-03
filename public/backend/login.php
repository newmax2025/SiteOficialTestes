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

// Garante que as sessões não sejam perdidas
ini_set("session.gc_maxlifetime", 86400); // 24 horas
session_set_cookie_params(86400); // Cookie da sessão válido por 24 horas

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

if (verificarLogin($conexao, $user, $pass, "clientes", "usuario", "aM.html", true)) {
    exit();
}

if (verificarLogin($conexao, $user, $pass, "admin", "admin", "admin.html", false)) {
    exit();
}

echo json_encode(["success" => false, "message" => "Usuário ou senha inválidos"]);
exit();

// Função para verificar login
function verificarLogin($conexao, $user, $pass, $tabela, $sessao, $redirect, $verificarStatus) {
    global $conexao; // Certifica-se de usar a conexão global

    // Se for clientes, buscamos o status; se for admin, não
    $sql = $verificarStatus 
        ? "SELECT id, senha, status FROM $tabela WHERE usuario = ?" 
        : "SELECT id, senha FROM $tabela WHERE usuario = ?";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Se a verificação de status for necessária e o usuário estiver inativo, bloqueia o login
        if ($verificarStatus && isset($row["status"]) && $row["status"] === "inativo") {
            echo json_encode(["success" => false, "message" => "Conta inativa. Entre em contato com o suporte."]);
            return false;
        }

        if (password_verify($pass, $row["senha"])) {
            // Registra a sessão corretamente
            session_regenerate_id(true); // Evita session fixation
            $_SESSION[$sessao] = $user;
            $_SESSION["usuario_id"] = $row["id"]; // Salva o ID do usuário para futuras consultas
            echo json_encode(["success" => true, "redirect" => $redirect]);
            return true;
        }
    }
    return false;
}
?>
