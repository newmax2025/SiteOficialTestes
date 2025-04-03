<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta CPF</title>
</head>
<body>
    <h2>Consulta de CPF</h2>
    <input type="text" id="cpf" placeholder="Digite o CPF">
    <button onclick="consultarCPF()">Consultar</button>
    <pre id="resultado"></pre>

    <script>
        function consultarCPF() {
            let cpf = document.getElementById("cpf").value.replace(/\D/g, ''); // Remove caracteres não numéricos
            if (cpf.length !== 11) {
                alert("Digite um CPF válido com 11 números.");
                return;
            }

            let url = `backend.php?cpf=${cpf}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    document.getElementById("resultado").textContent = JSON.stringify(data, null, 4);
                })
                .catch(error => {
                    document.getElementById("resultado").textContent = "Erro na consulta.";
                    console.error("Erro:", error);
                });
        }
    </script>
</body>
</html>
