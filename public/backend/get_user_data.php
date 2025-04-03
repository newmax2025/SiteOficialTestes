<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(["autenticado" => false]);
    exit;
}

// Exemplo de retorno de dados do usuário autenticado
echo json_encode([
    "autenticado" => true,
    "nome" => $_SESSION['usuario']['nome'],
    "whatsapp" => $_SESSION['usuario']['whatsapp'],
    "status" => $_SESSION['usuario']['status']
]);
