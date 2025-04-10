<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Consulta Desmascara Pix</title>
    <link rel="stylesheet" href="../../assets/css/consultas.css?v=<?php echo md5_file('../../assets/css/consultas.css'); ?>">
    <script>
        fetch("../../backend/verifica_sessao.php")
            .then(response => response.json())
            .then(data => {
                if (!data.autenticado) {
                    window.location.href = "../login.php";
                }
            })
            .catch(error => {
                console.error("Erro ao verificar sessão:", error);
                window.location.href = "../login.php";
            });
    </script>
</head>

<body>
    <div class="container">
        <div class="logo-container">
        <img class="logo" src="../../assets/img/new_max_buscas.png" alt="Logo do Cliente">
        </div>
        <h2>Consulta Pix</h2>
        <input type="text" id="cpf" placeholder="Digite o CPF ou parte dele" maxlength="14">
        <input type="text" id="nome" placeholder="Digite o nome ou parte dele" maxlength="50">
        <button id="consultarBtn" onclick="consultarCPF()" disabled>Consultar</button>

        <!-- Turnstile CAPTCHA -->
        <div class="cf-turnstile" id="captcha" data-sitekey="0x4AAAAAABDPzCDp7OiEAfvh" data-callback="onCaptchaSuccess">
        </div>

        <input type="hidden" id="captcha-response" name="cf-turnstile-response">

        <p id="resultado"></p>

        <div id="dados" class="dados" style="display: none;"></div>

        <!-- Botões de ação -->
        <div id="acoes" style="display: none; margin-top: 20px;">
            <button onclick="copiarDados()">Copiar Dados</button>
            <button style="margin-top: 20px;" onclick="baixarPDF()">Baixar em PDF</button>
            <button style="margin-top: 20px;" onclick="baixarTXT()">Baixar em TXT</button>
        </div>

    </div>

     <div id="dados" class="dados" style="display: none;">
            <!-- Botão para baixar PDF -->
            <button id="baixarPDFBtn" onclick="baixarPDF()" style="margin-top: 15px;">Baixar PDF</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script src="../../assets/js/consultas/consulta_pix.js?v=<?php echo md5_file('../../assets/js/consultas/consulta_pix.js'); ?>"></script>
    <script src="../../assets/js/consultas/baixar_consultas.js?v=<?php echo md5_file('../../assets/js/consultas/baixar_consultas.js'); ?>"></script>
</body>

</html>
