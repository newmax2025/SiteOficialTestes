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
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            padding: 40px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        select {
            padding: 12px;
            width: 100%;
            font-size: 16px;
            margin-top: 20px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .recarga-btn {
            padding: 12px 20px;
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .recarga-btn:hover {
            background-color: #218838;
        }

        .voltar {
            margin-top: 30px;
            display: inline-block;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .voltar:hover {
            text-decoration: underline;
        }
    </style>
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
