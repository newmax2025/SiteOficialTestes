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
    <link rel="stylesheet" href="../assets/css/aM.css?v=<?php echo md5_file('../assets/css/aM.css'); ?>">
    <style>
        body {
    font-family: Arial, sans-serif;
    background: #f5f7fa;
    padding: 40px;
    color: black;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    max-width: 500px;
    margin: auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

h2 {
    margin-top: 0;
}

.info {
    margin-bottom: 15px;
}

.label {
    font-weight: bold;
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
    <input type="checkbox" id="check">
    <label for="check">
        <i class="fas fa-bars" id="btn"></i>
        <i class="fas fa-times" id="cancel"></i>
    </label>
    <div class="sidebar">
        <header>Menu</header>
        <ul>
            <a href="aM.php">Voltar</a>
        </ul>
    </div>
    <header>
        <h1></h1>
    </header>
    <div class="card" id="usuario-card">
        <h2>Carregando...</h2>
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
                    <a href="${dados.whatsapp}" class="whatsapp-link" target="_blank">Falar no WhatsApp</a>
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