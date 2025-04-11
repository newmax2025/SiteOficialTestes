<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recarga de Saldo</title>
    <link rel="stylesheet" href="../assets/css/aM.css?v=<?php echo md5_file('../assets/css/aM.css'); ?>">
    <link rel="stylesheet" href="../assets/css/recarga.css?v=<?php echo md5_file('../assets/css/recarga.css'); ?>">
</head>
<body>
    <div class="container">
        <h1>Recarga de Saldo</h1>
        <label for="recarga_valor">Selecione o valor da recarga:</label>
        <select id="recarga_valor">
            <option value="10">R$10,00</option>
            <option value="20">R$20,00</option>
            <option value="30">R$30,00</option>
            <option value="50">R$50,00</option>
            <option value="100">R$100,00</option>
        </select>
        <button id="recargaButton">Adicionar Saldo</button>
        <div id="recarga_result"></div>
    </div>
    <script src="../assets/js/recarga.js"></script>
</body>
</html>
