<?php
session_start();
header('Content-Type: application/json');

// Configuração do banco de dados
$host = "mysql.hostinger.com";
$dbname = "u377990636_DataBase";
$username = "u377990636_Admin";
$password = "+c4Nrz@H5";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Erro ao conectar ao banco de dados."]));
}

// Recebendo os dados do POST
$data = json_decode(file_get_contents("php://input"), true);
$user = $data["username"];
$pass = $data["password"];

// Validação básica
if (empty($user) || empty($pass)) {
    die(json_encode(["success" => false, "message" => "Preencha todos os campos!"]));
}

// Função para verificar login
function verificarLogin($conn, $user, $pass, $tabela, $sessao, $redirect) {
    $sql = "SELECT senha FROM $tabela WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
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
verificarLogin($conn, $user, $pass, "clientes", "usuario", "AM.html");

// Se não encontrou, verifica na tabela admin
verificarLogin($conn, $user, $pass, "admin", "admin", "admin.html");

// Se não encontrou em nenhuma das tabelas
echo json_encode(["success" => false, "message" => "Usuário ou senha inválidos"]);

$conn->close();
?>
