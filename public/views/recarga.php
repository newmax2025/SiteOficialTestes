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
            max-width: 600px;
            margin: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            margin-top: 0;
            color: #333;
        }

        .btn-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }

        .recarga-btn {
            padding: 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
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
        <h2>Escolha um valor para recarregar</h2>

        <div class="btn-grid">
            <form action="processar_recarga.php" method="POST">
                <button class="recarga-btn" name="valor" value="10">R$ 10</button>
            </form>
            <form action="processar_recarga.php" method="POST">
                <button class="recarga-btn" name="valor" value="20">R$ 20</button>
            </form>
            <form action="processar_recarga.php" method="POST">
                <button class="recarga-btn" name="valor" value="30">R$ 30</button>
            </form>
            <form action="processar_recarga.php" method="POST">
                <button class="recarga-btn" name="valor" value="50">R$ 50</button>
            </form>
            <form action="processar_recarga.php" method="POST">
                <button class="recarga-btn" name="valor" value="100">R$ 100</button>
            </form>
        </div>

        <a href="aM.php" class="voltar">← Voltar para área de membros</a>
    </div>

</body>
</html>
