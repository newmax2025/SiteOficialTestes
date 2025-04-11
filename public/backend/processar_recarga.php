<?php
session_start();
header('Content-Type: application/json');
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$valor = isset($data['amount']) ? (float)$data['amount'] : 0;


if (!isset($_SESSION['usuario']) || $valor <= 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos ou usuário não autenticado.']);
    exit();
}

$usuario = $_SESSION['usuario'];

// Atualiza o saldo do usuário
$sql = "UPDATE clientes SET saldo = saldo + ? WHERE usuario = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ds", $valor, $usuario);

if ($stmt->execute()) {
    echo json_encode(['sucesso' => true, 'mensagem' => 'Recarga realizada com sucesso!']);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao processar a recarga.']);
}

$stmt->close();
$conexao->close();
?>
