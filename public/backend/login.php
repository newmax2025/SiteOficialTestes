<?php
session_start();
header('Content-Type: application/json');
ob_start(); // Evita saída antes do JSON
error_reporting(0); // Desativa exibição de erros para evitar saída inesperada

require 'config.php';

// Recebe os dados do POST
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["username"], $data["password"])) {
    echo json_encode(["success" => false, "message" => "Preencha todos os campos!"]);
    exit();
}

$user = trim($data["username"]);
$pass = trim($data["password"]);

// Verifica primeiro na tabela clientes
if (verificarLogin($conexao, $user, $pass, "clientes", "usuario", "aM.html")) {
    exit();
}

// Se não encontrou, verifica na tabela admin
if (verificarLogin($conexao, $user, $pass, "admin", "admin", "admin.html")) {
    exit();
}

// Se não encontrou em nenhuma das tabelas
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
