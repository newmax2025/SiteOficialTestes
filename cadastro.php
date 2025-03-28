<?php
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

// Recebendo os dados
$data = json_decode(file_get_contents("php://input"), true);
$user = $data["username"];
$pass = $data["password"];

// Validação básica
if (empty($user) || empty($pass)) {
    die(json_encode(["success" => false, "message" => "Preencha todos os campos!"]));
}

// Verifica se o usuário já existe
$sql = "SELECT id FROM clientes WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die(json_encode(["success" => false, "message" => "Usuário já cadastrado."]));
}

// Criptografar a senha antes de salvar
$hashed_password = password_hash($pass, PASSWORD_DEFAULT);

// Insere no banco de dados
$sql = "INSERT INTO clientes (usuario, senha) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user, $hashed_password);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Usuário cadastrado com sucesso!"]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao cadastrar usuário."]);
}

$stmt->close();
$conn->close();
?>
