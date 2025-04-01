<?php
require 'config.php'; // Mantém a conexão com o banco de dados sem alterar o config.php

// Buscar a chave secreta do Turnstile na tabela 'configuracoes'
$query = "SELECT chave FROM configuracoes LIMIT 1";
$stmt = $conexao->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$cloudflare_secret = $row['chave'] ?? null;

if (!$cloudflare_secret) {
    die("Erro: Chave do Cloudflare não encontrada no banco de dados.");
}
?>
