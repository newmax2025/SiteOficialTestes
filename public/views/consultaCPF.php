<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Consulta CPF</title>
    <link rel="stylesheet" href="../assets/css/consultaCPF.css?v=<?php echo md5_file('../assets/css/consultaCPF.css'); ?>">

    <!-- Verificação de sessão -->
    <script>
        fetch("../backend/verifica_sessao.php")
            .then(response => response.json())
            .then(data => {
                if (!data.autenticado) {
                    window.location.href = "login.php";
                }
            })
            .catch(error => {
                console.error("Erro ao verificar sessão:", error);
                window.location.href = "login.php";
            });
    </script>

    <!-- Bibliotecas para PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="logo-container">
            <img class="logo" src="../assets/img/New Max Buscas.png" alt="Logo do Cliente">
        </div>

        <h2>Consulta CPF Comum</h2>
        <input type="text" id="cpf" placeholder="Digite o CPF" maxlength="14" oninput="formatCPF(this)">
        <button id="consultarBtn" onclick="consultarCPF()" disabled>Consultar</button>

        <!-- CAPTCHA -->
        <div class="cf-turnstile" id="captcha" data-sitekey="0x4AAAAAABDPzCDp7OiEAfvh" data-callback="onCaptchaSuccess"></div>
        <input type="hidden" id="captcha-response" name="cf-turnstile-response">

        <p id="resultado"></p>

        <!-- Resultado da consulta -->
        <div id="dados" class="dados" style="display: none;">
            <!-- Aqui os dados da consulta são inseridos -->
            
            <!-- Botão de PDF -->
            <button id="baixarPDFBtn" onclick="baixarPDF()" style="margin-top: 15px;">Baixar PDF</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script src="../assets/js/consultaCPF.js?v=<?php echo md5_file('../assets/js/consultaCPF.js'); ?>"></script>

    <script>
        async function baixarPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const elemento = document.getElementById("dados");

            await html2canvas(elemento).then(canvas => {
                const imgData = canvas.toDataURL("image/png");
                const imgProps = doc.getImageProperties(imgData);
                const pdfWidth = doc.internal.pageSize.getWidth();
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                doc.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
                doc.save("consulta-cpf.pdf");
            });
        }
    </script>
</body>

</html>
