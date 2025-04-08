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

        <!-- Botão para baixar PDF (fica oculto até gerar os dados) -->
        <button id="baixarPDFBtn" onclick="baixarPDF()" style="margin-top: 15px; display: none;">Baixar PDF</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script src="../assets/js/consultaCPF.js?v=<?php echo md5_file('../assets/js/consultaCPF.js'); ?>"></script>

    <script>
        // Função para formatar CPF com máscara
        function formatCPF(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 3) value = value.replace(/^(\d{3})(\d)/, '$1.$2');
            if (value.length > 6) value = value.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
            if (value.length > 9) value = value.replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3-$4');
            input.value = value;
        }

        // Habilita o botão após validação do CAPTCHA
        function onCaptchaSuccess(token) {
            document.getElementById("captcha-response").value = token;
            document.getElementById("consultarBtn").disabled = false;
        }

        // Exemplo de consulta CPF (substitua com sua lógica real/fetch)
        function consultarCPF() {
            const cpf = document.getElementById("cpf").value;
            const dadosDiv = document.getElementById("dados");
            const btnPDF = document.getElementById("baixarPDFBtn");

            // Simulação de resultado (você pode trocar pelo fetch real do backend)
            const resultado = {
                nome: "João da Silva",
                cpf: cpf,
                nascimento: "10/05/1990",
                situacao: "Regular"
            };

            dadosDiv.innerHTML = `
                <p><strong>Nome:</strong> ${resultado.nome}</p>
                <p><strong>CPF:</strong> ${resultado.cpf}</p>
                <p><strong>Nascimento:</strong> ${resultado.nascimento}</p>
                <p><strong>Situação:</strong> ${resultado.situacao}</p>
            `;
            dadosDiv.style.display = "block";
            btnPDF.style.display = "inline-block"; // Exibe o botão após a consulta
        }

        // Geração do PDF
        async function baixarPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const dadosElement = document.getElementById("dados");
            const texto = dadosElement.innerText.split('\n');

            doc.setFontSize(16);
            doc.text("Resultado da Consulta CPF", 20, 20);
            doc.setFontSize(12);

            let y = 40;
            texto.forEach(linha => {
                doc.text(linha, 20, y);
                y += 10;
            });

            doc.save("consulta-cpf.pdf");
        }
    </script>
</body>

</html>
