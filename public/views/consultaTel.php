<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Consulta Telefone</title>
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
        <h2>Consulta Telefone</h2>
        <input type="text" id="tel" placeholder="Digite o Telefone" maxlength="15" oninput="formatTel(this)">
        <button id="consultarBtn" onclick="consultarTel()" disabled>Consultar</button>

        <!-- Turnstile CAPTCHA -->
        <div class="cf-turnstile" id="captcha" data-sitekey="0x4AAAAAABDPzCDp7OiEAfvh" data-callback="onCaptchaSuccess">
        </div>

        <input type="hidden" id="captcha-response" name="cf-turnstile-response">

        <p id="resultado"></p>

        <div id="dados" class="dados" style="display: none;"></div>
         <!-- BOTÃO DE BAIXAR PDF -->
              <button onclick="baixarPDF()">Baixar PDF</button>
    </div>

    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script src="../assets/js/consultaTel.js?v=<?php echo md5_file('../assets/js/consultaTel.js'); ?>"></script>
</body>

</html>
