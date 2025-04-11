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
    <title>Página do Usuário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/aM.css?v=<?php echo md5_file('../assets/css/aM.css'); ?>">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 50px 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
            text-align: center;
        }

        h2 {
            margin-top: 0;
            color: #2c3e50;
        }

        .info {
            margin: 15px 0;
            font-size: 1.1rem;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .whatsapp-link {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            color: white;
            background-color: #25D366;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .whatsapp-link:hover {
            background-color: #1ebe5d;
        }

        .loader {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .sidebar {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .sidebar a {
            background: #2c3e50;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background: #1a242f;
        }

    </style>
</head>
<body>
    <div class="sidebar">
        <a href="aM.php"><i class="fas fa-arrow-left"></i> Voltar</a>
    </div>

    <div class="card" id="usuario-card">
        <div class="loader"></div>
        <p>Carregando dados do usuário...</p>
    </div>

    <script>
        async function carregarDadosUsuario() {
            try {
                const resposta = await fetch("../backend/get_user_data.php");
                const dados = await resposta.json();

                if (!dados.autenticado) {
                    document.getElementById("usuario-card").innerHTML = `
                        <h2>Usuário não autenticado</h2>
                        <p>Por favor, faça login novamente.</p>
                    `;
                    return;
                }

                document.getElementById("usuario-card").innerHTML = `
                    <h2>Bem-vindo, ${dados.usuario}!</h2>
                    <div class="info"><span class="label">Plano:</span> ${dados.plano}</div>
                    <div class="info"><span class="label">Saldo:</span> R$ ${parseFloat(dados.saldo).toFixed(2)}</div>
                    <div class="info"><span class="label">Vendedor:</span> ${dados.nome}</div>
                    <a href="${dados.whatsapp}" class="whatsapp-link" target="_blank">
                        <i class="fa-brands fa-whatsapp"></i> Falar no WhatsApp
                    </a>
                `;
            } catch (erro) {
                document.getElementById("usuario-card").innerHTML = `
                    <h2>Erro ao carregar os dados</h2>
                    <p>${erro}</p>
                `;
            }
        }

        carregarDadosUsuario();
    </script>
</body>
</html>
