<?php
session_start();
header('Content-Type: application/json');
require 'config.php';

// Verifica se o usuário está autenticado
if (!isset($_SESSION["usuario"])) {
    echo json_encode(["autenticado" => false]);
    exit();
}

$usuario = $_SESSION["usuario"];

$sql = "SELECT c.usuario, c.plano, v.nome AS revendedor_nome, v.whatsapp AS revendedor_whatsapp 
        FROM clientes c
        LEFT JOIN vendedores v ON c.vendedor_id = v.id
        WHERE c.usuario = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $dados = $result->fetch_assoc();

    echo json_encode([
        "autenticado" => true,
        "nome" => $dados["revendedor_nome"] ?? "Não informado",
        "whatsapp" => !empty($dados["revendedor_whatsapp"]) ? "https://wa.me/".$dados["revendedor_whatsapp"] : "#",
        "plano" => $dados["plano"]
    ]);
} else {
    echo json_encode(["autenticado" => false]);
}

$stmt->close();
$conexao->close();
?>
