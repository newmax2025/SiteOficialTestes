<?php
session_start();
header('Content-Type: application/json');

// Importa a configuração do banco
require 'config.php';

// Recebendo os dados do POST
$data = json_decode(file_get_contents("php://input"), true);
$user = $data["username"];
$pass = $data["password"];

// Validação básica
if (empty($user) || empty($pass)) {
    die(json_encode(["success" => false, "message" => "Preencha todos os campos!"]));
}

// Função para verificar login
function verificarLogin($conexao, $user, $pass, $tabela, $sessao, $redirect) {
    $sql = "SELECT senha FROM $tabela WHERE usuario = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verifica a senha com password_verify
        if (password_verify($pass, $row["senha"])) {
            $_SESSION[$sessao] = $user;
            echo json_encode(["success" => true, "redirect" => $redirect]);
            exit();
        }
    }
}

// Verifica primeiro na tabela clientes
verificarLogin($conexao, $user, $pass, "clientes", "usuario", "AM.html");

// Se não encontrou, verifica na tabela admin
verificarLogin($conexao, $user, $pass, "admin", "admin", "admin.html");

// Se não encontrou em nenhuma das tabelas
echo json_encode(["success" => false, "message" => "Usuário ou senha inválidos"]);

$conexao->close();
?>
