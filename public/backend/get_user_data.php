<?php
session_start(); // Inicia a sessão
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(["autenticado" => false]);
    exit;
}

// Retorna os dados do usuário autenticado
echo json_encode([
    "autenticado" => true,
    "nome" => $_SESSION['usuario']['nome'],
    "whatsapp" => $_SESSION['usuario']['whatsapp'],
    "status" => $_SESSION['usuario']['status']
]);
?>
