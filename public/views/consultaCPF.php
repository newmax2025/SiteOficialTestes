<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Consulta CPF</title>
    <link rel="stylesheet" href="../assets/css/consultaCPF.css?v=<?php echo md5_file('../assets/css/consultaCPF.css'); ?>">
    <script>
        fetch("../backend/verifica_sessao.php")
    .then(response => response.json())
    .then(data => {
        if (!data.autenticado) {
            window.location.href = "login.php"; // Redireciona se não estiver autenticado
        }
    })
    .catch(error => {
        console.error("Erro ao verificar sessão:", error);
        window.location.href = "login.php"; // Opcional: Redireciona em caso de erro
    });

        </script>
</head>

<body>
    <div class="container">
        <div class="logo-container">
            <img class="logo" src="../assets/img/New Max Buscas.png" alt="Logo do Cliente">
        </div>
        <h2>Consulta CPF Comum</h2>
        <input type="text" id="cpf" placeholder="Digite o CPF" maxlength="14" oninput="formatCPF(this)">
        <button id="consultarBtn" onclick="consultarCPF()" disabled>Consultar</button>

        <!-- Turnstile CAPTCHA -->
        <div class="cf-turnstile" id="captcha" data-sitekey="0x4AAAAAABDPzCDp7OiEAfvh" data-callback="onCaptchaSuccess">
        </div>

        <input type="hidden" id="captcha-response" name="cf-turnstile-response">

        <p id="resultado"></p>

        <div id="dados" class="dados" style="display: none;"></div>

    </div>

     <div id="dados" class="dados" style="display: none;">
            <!-- Botão para baixar PDF -->
            <button id="baixarPDFBtn" onclick="baixarPDF()" style="margin-top: 15px;">Baixar PDF</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script src="../assets/js/consultaCPF.js?v=<?php echo md5_file('../assets/js/consultaCPF.js'); ?>"></script>
</body>

</html>
