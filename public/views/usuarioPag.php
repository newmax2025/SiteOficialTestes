<?php
// Dados simulados (você pode puxar isso de um banco de dados futuramente)
$usuario = [
    'nome' => 'João da Silva',
    'status' => 'Ativo',
    'plano' => 'Premium',
    'vendedor' => [
        'nome' => 'Carlos Vendas',
        'whatsapp' => '5511999999999' // no formato código do país + número
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Página do Usuário</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            padding: 40px;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            margin-top: 0;
            color: #333;
        }
        .info {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .whatsapp-link {
            display: inline-block;
            margin-top: 5px;
            color: white;
            background-color: #25D366;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }
        .whatsapp-link:hover {
            background-color: #1ebe5d;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Bem-vindo, <?php echo htmlspecialchars($usuario['nome']); ?>!</h2>

        <div class="info">
            <span class="label">Status:</span> <?php echo $usuario['status']; ?>
        </div>
        <div class="info">
            <span class="label">Plano:</span> <?php echo $usuario['plano']; ?>
        </div>
        <div class="info">
            <span class="label">Vendedor Responsável:</span><br>
            <?php echo $usuario['vendedor']['nome']; ?><br>
            <a class="whatsapp-link" 
               href="https://wa.me/<?php echo $usuario['vendedor']['whatsapp']; ?>" 
               target="_blank">Falar no WhatsApp</a>
        </div>
    </div>
</body>
</html>
