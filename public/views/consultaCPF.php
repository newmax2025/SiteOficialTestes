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
                window.location.href = "login.php";
            }
        })
        .catch(error => {
            console.error("Erro ao verificar sessão:", error);
            window.location.href = "login.php";
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
        <div class="cf-turnstile" id="captcha" data-sitekey="0x4AAAAAABDPzCDp7OiEAfvh" data-callback="onCaptchaSuccess"></div>
        <input type="hidden" id="captcha-response" name="cf-turnstile-response">

        <p id="resultado"></p>

        <div id="dados" class="dados" style="display: none;">
            <!-- Aqui vão os dados retornados pela consulta -->
        </div>

        <!-- Botão para baixar PDF -->
        <div style="margin-top: 15px; display: none;" id="pdfContainer">
            <button id="baixarPDFBtn" onclick="baixarPDF()">Baixar PDF</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script src="../assets/js/consultaCPF.js?v=<?php echo md5_file('../assets/js/consultaCPF.js'); ?>"></script>

    <script>
        // Mostra o botão PDF apenas se houver dados
        function mostrarPDFBtn() {
            document.getElementById("pdfContainer").style.display = "block";
        }

        // Função para gerar PDF com os dados da consulta
        async function baixarPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const dadosDiv = document.getElementById("dados");
            const texto = dadosDiv.innerText || "Nenhum dado disponível";

            doc.text("Resultado da Consulta por CPF", 10, 10);
            const linhas = doc.splitTextToSize(texto, 180); // Quebra em múltiplas linhas
            doc.text(linhas, 10, 20);

            doc.save("consulta-cpf.pdf");
        }

        // Exemplo: chamada após sucesso da consulta
        function exibirDadosNaTela(dados) {
            const dadosDiv = document.getElementById("dados");
            dadosDiv.style.display = "block";
            dadosDiv.innerText = dados;
            mostrarPDFBtn(); // Mostra botão PDF
        }

        // Essa função deve ser chamada no seu JS original (consultaCPF.js)
        // após o fetch/consulta com os dados recebidos.
    </script>
</body>
</html>
