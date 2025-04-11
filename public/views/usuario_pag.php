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
    <link rel="stylesheet" href="../assets/css/usuario_pag.css?v=<?php echo md5_file('../assets/css/usuario_pag.css'); ?>">
    <style>
        

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
    <a href="recarga.php" class="saldo-btn">
        <i class="fas fa-wallet"></i> Adicionar mais saldo
    </a>
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
