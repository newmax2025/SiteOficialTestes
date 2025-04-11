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
</head>
<body>

    <div class="card">
        <h2>Recarregar Saldo</h2>

        <form action="processar_recarga.php" method="POST">
            <label for="valor">Escolha o valor da recarga:</label>
            <select name="valor" id="valor" required>
                <option value="" disabled selected>Selecione um valor</option>
                <option value="10">R$ 10,00</option>
                <option value="20">R$ 20,00</option>
                <option value="30">R$ 30,00</option>
                <option value="50">R$ 50,00</option>
                <option value="100">R$ 100,00</option>
            </select>

            <button type="submit" class="recarga-btn">Confirmar Recarga</button>
        </form>

        <a href="aM.php" class="voltar">← Voltar para área de membros</a>
    </div>

</body>
</html>
