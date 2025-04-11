<?php
session_start();
header('Content-Type: application/json');
require '../config.php';

if (!isset($_SESSION["usuario"]) || !isset($_SESSION["usuario"]["usuario"])) {
    echo json_encode(["autenticado" => false]);
    exit();
}

$usuario = $_SESSION["usuario"]["usuario"];

$sql = "SELECT c.usuario, c.plano, c.saldo, v.nome AS revendedor_nome, v.whatsapp AS revendedor_whatsapp 
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
        "usuario" => $dados["usuario"],
        "nome" => $dados["revendedor_nome"] ?? "Não informado",
        "whatsapp" => !empty($dados["revendedor_whatsapp"]) ? "https://wa.me/".$dados["revendedor_whatsapp"] : "#",
        "plano" => $dados["plano"] ?? "Não definido",
        "saldo" => isset($dados["saldo"]) ? number_format($dados["saldo"], 2, ',', '.') : "0,00"
    ]);
} else {
    echo json_encode(["autenticado" => false]);
}

$stmt->close();
$conexao->close();
?>
