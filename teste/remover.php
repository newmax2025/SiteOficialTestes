<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuração do banco de dados
$host = "mysql.hostinger.com";
$dbname = "u377990636_DataBase";
$username = "u377990636_Admin";
$password = "+c4Nrz@H5";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Erro ao conectar ao banco de dados: " . $conn->connect_error]));
}

// Recebe os dados
$data = json_decode(file_get_contents("php://input"), true);
$user = isset($data["username"]) ? trim($data["username"]) : "";

if (empty($user)) {
    die(json_encode(["success" => false, "message" => "Usuário não informado."]));
}

// Confirma se o usuário existe antes de remover
$checkUser = $conn->prepare("SELECT usuario FROM clientes WHERE usuario = ?");
$checkUser->bind_param("s", $user);
$checkUser->execute();
$checkUser->store_result();

if ($checkUser->num_rows === 0) {
    die(json_encode(["success" => false, "message" => "Usuário não encontrado."]));
}
$checkUser->close();

// Remove do banco de dados
$sql = "DELETE FROM clientes WHERE usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao remover usuário: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
