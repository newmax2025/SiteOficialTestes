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
            captchaContainer. innerHTML = ""; // Remove o CAPTCHA antigo
            turnstile.render("#captcha", {
                sitekey: "0x4AAAAAABDPzCDp7OiEAfvh",
                callback: onCaptchaSuccess,
            });
        } else {
            console.warn("Elemento CAPTCHA não encontrado!");
        }
    }, 500); // Aguarda 500ms antes de recriar o CAPTCHA
}

function exibirCampo(label, valor) {
    return `<p><strong>${label}:</strong> ${valor ?? "Não disponível"}</p>`;
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
        let html = "";

        const info = dados.personal_info || {};
        html += "<h3>Informações Pessoais</h3>";
        html += exibirCampo("Nome", info.name);
        html += exibirCampo("CPF", formatarCPF(info.document_number));
        html += exibirCampo("Nascimento", info.birthday_date);
        html += exibirCampo("Sexo", info.gender === "M" ? "Masculino" : "Feminino");
        html += exibirCampo("Nome da Mãe", info.mother_name);
        html += exibirCampo("Nome do Pai", info.father_name);
        html += exibirCampo("Nacionalidade", info.nationality);
        html += exibirCampo("Renda", info.income);

        const status = dados.status?.registration_status || {};
        html += "<h3>Status</h3>";
        html += exibirCampo("Status Receita", status.description);
        html += exibirCampo("Data", status.date);
        html += exibirCampo("Óbito", dados.status?.death ? "Sim" : "Não");

        const score = dados.score || {};
        html += "<h3>Score</h3>";
        html += exibirCampo("Score CSBA", score.score_csba);
        html += exibirCampo("Risco", score.score_csba_risk_range);

        const serasa = dados.serasa?.new_mosaic || {};
        html += "<h3>Perfil Serasa</h3>";
        html += exibirCampo("Código", serasa.code);
        html += exibirCampo("Descrição", serasa.description);
        html += exibirCampo("Classe", serasa.class);

        const poderCompra = dados.purchasing_power || {};
        html += "<h3>Poder de Compra</h3>";
        html += exibirCampo("Descrição", poderCompra.description);
        html += exibirCampo("Faixa", poderCompra.range);
        html += exibirCampo("Renda Estimada", poderCompra.income);

        html += "<h3>Endereços</h3>";
        if (dados.addresses?.length) {
            dados.addresses.forEach(end => {
                html += `<p>${end.type || ""} ${end.place || ""}, ${end.number || "s/n"} - ${end.neighborhood || ""}, ${end.city || ""} - ${end.state || ""} (${end.zip_code || ""})</p>`;
            });
        } else {
            html += "<p>Não disponível</p>";
        }

        html += "<h3>Telefones</h3>";
        if (dados.phones?.length) {
            html += dados.phones.map(tel => `<p>${tel.number}</p>`).join("");
        } else {
            html += "<p>Não disponível</p>";
        }

        html += "<h3>Empregos</h3>";
        if (dados.jobs?.length) {
            dados.jobs.forEach(emp => {
                html += `<p><strong>Empresa:</strong> ${emp.trade_name || "N/A"} | <strong>Admissão:</strong> ${emp.admission_date} | <strong>Saída:</strong> ${emp.termination_date}</p>`;
            });
        } else {
            html += "<p>Não disponível</p>";
        }

        html += "<h3>Vacinas</h3>";
        if (dados.vaccines?.length) {
            dados.vaccines.forEach(vac => {
                html += `<p><strong>${vac.vaccine}</strong> - ${vac.dose}, ${vac.date} - ${vac.establishment}</p>`;
            });
        } else {
            html += "<p>Não disponível</p>";
        }

        html += "<h3>Interesses</h3>";
        const interesses = dados.interests || {};
        for (const [chave, valor] of Object.entries(interesses)) {
            const nome = chave.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            html += exibirCampo(nome, typeof valor === "boolean" ? (valor ? "Sim" : "Não") : valor);
        }

        dadosElement.innerHTML = html;
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
