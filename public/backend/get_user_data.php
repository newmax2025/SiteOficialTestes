<?php
session_start();
include("../backend/config.php"); // Certifique-se de incluir seu arquivo de conexão

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["autenticado" => false]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Consulta ao banco de dados
$sql = "SELECT nome, whatsapp, status FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        "autenticado" => true,
        "nome" => $user['nome'],
        "whatsapp" => $user['whatsapp'],
        "status" => $user['status']
    ]);
} else {
    echo json_encode(["autenticado" => false]);
}

$stmt->close();
$conn->close();
?>
