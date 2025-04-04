let captchaValidado = false;

function onCaptchaSuccess() {
    captchaValidado = true;
    document.getElementById("consultarBtn").disabled = false;
}

function resetCaptcha() {
    captchaValidado = false; // Reseta a validação do CAPTCHA
    document.getElementById("consultarBtn").disabled = true; // Desativa o botão

    setTimeout(() => {
        const captchaContainer = document.getElementById("captcha");
        if (captchaContainer) {
            captchaContainer.innerHTML = ""; // Remove o CAPTCHA antigo
            turnstile.render("#captcha", {
                sitekey: "0x4AAAAAABDPzCDp7OiEAfvh",
                callback: onCaptchaSuccess,
            });
        } else {
            console.warn("Elemento CAPTCHA não encontrado!");
        }
    }, 500); // Aguarda 500ms antes de recriar o CAPTCHA
}

function consultarCPF() {
    if (!captchaValidado) {
        document.getElementById("resultado").innerText = "Por favor, resolva o CAPTCHA.";
        return;
    }

    const consultarBtn = document.getElementById("consultarBtn");
    consultarBtn.disabled = true;

    const cpfInput = document.getElementById("cpf");
    const cpf = cpfInput.value;
    const resultadoElement = document.getElementById("resultado");
    const dadosElement = document.getElementById("dados");

    if (cpf.length < 14) {
        resultadoElement.innerText = "CPF inválido!";
        return;
    }

    resultadoElement.innerText = "Consultando...";
    dadosElement.style.display = "none";

    const localApiUrl = "../backend/api.php";
    const cpfLimpo = cpf.replace(/\D/g, "");

    fetch(localApiUrl, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ cpf: cpfLimpo }),
    })
        .then((response) => {
            if (!response.ok) throw new Error(`Erro na consulta (${response.status}).`);
            return response.json();
        })
        .then((data) => {
            if (!data || !data.data) throw new Error("Nenhuma informação encontrada para este CPF.");

            const dados = data.data;
            document.getElementById("nome").innerText = dados.dados_basicos?.nome || "Não disponível";
            document.getElementById("cpf_resultado").innerText = formatarCPF(dados.dados_basicos?.cpf || "") || "Não disponível";
            document.getElementById("safra").innerText = dados.dados_basicos?.safra || "Não disponível";
            document.getElementById("nascimento").innerText = dados.dados_basicos?.nascimento || "Não disponível";
            document.getElementById("nome_mae").innerText = dados.dados_basicos?.nome_mae || "Não disponível";
            document.getElementById("sexo").innerText = dados.dados_basicos?.sexo === "M" ? "Masculino" : "Feminino";
            document.getElementById("email").innerText = dados.dados_basicos?.email || "Não disponível";
            document.getElementById("obito").innerText = dados.dados_basicos?.obito?.status || "Não disponível";
            document.getElementById("status_receita").innerText = dados.dados_basicos?.status_receita || "Não disponível";
            document.getElementById("cbo").innerText = dados.dados_basicos?.cbo || "Não disponível";
            document.getElementById("faixa_renda").innerText = dados.dados_basicos?.faixa_renda || "Não disponível";

            document.getElementById("veiculos").innerText = dados.veiculos?.length > 0 ? dados.veiculos.join(", ") : "Não disponível";
            document.getElementById("telefones").innerText = dados.telefones?.length > 0 ? dados.telefones.join(", ") : "Não disponível";
            document.getElementById("celulares").innerText = dados.celulares?.length > 0 ? dados.celulares.join(", ") : "Não disponível";

            document.getElementById("enderecos").innerText = dados.endereco ? `${dados.endereco.logradouro || "Não disponível"}, ${dados.endereco.cidade || "Não disponível"} - ${dados.endereco.uf || ""}` : "Não disponível";

            dadosElement.style.display = "block";
            resultadoElement.innerText = `Consulta realizada para o CPF: ${cpf}`;
        })
        .catch((error) => {
            console.error("Erro ao consultar CPF:", error);
            resultadoElement.innerText = `Erro: ${error.message}`;
            dadosElement.style.display = "none";
        })
        .finally(() => {
            consultarBtn.disabled = false;
            resetCaptcha(); // Agora recria o CAPTCHA corretamente
        });
}

function formatarCPF(cpf) {
    if (!cpf) return "";
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}

function formatCPF(input) {
    let value = input.value.replace(/\D/g, "");
    value = value.replace(/(\d{3})(\d)/, "$1.$2");
    value = value.replace(/(\d{3})(\d)/, "$1.$2");
    value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    input.value = value;
  }
