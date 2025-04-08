let captchaValidado = false;

const interessesLabels = {
    credit_personal_pre_approved: "Crédito Pessoal Pré-Aprovado",
    credit_real_estate_pre_approved: "Crédito Imobiliário Pré-Aprovado",
    vehicle_financing_pre_approved: "Financiamento de Veículos Pré-Aprovado",
    middle_class: "Classe Média",
    automatic_debit: "Débito Automático",
    has_luxury: "Possui Itens de Luxo",
    has_investments: "Possui Investimentos",
    has_credit_card: "Possui Cartão de Crédito",
    has_multiple_cards: "Possui Múltiplos Cartões",
    has_high_standard_account: "Conta de Alto Padrão",
    has_black_card: "Possui Cartão Black",
    has_prime_card: "Possui Cartão Prime",
    has_prepaid_cell: "Celular Pré-Pago",
    has_postpaid_cell: "Celular Pós-Pago",
    has_accumulated_miles: "Possui Milhas Acumuladas",
    has_own_house: "Possui Casa Própria",
    has_discounts: "Utiliza Descontos",
    has_checking_accounts: "Possui Conta Corrente",
    has_auto_insurance: "Possui Seguro Automotivo",
    has_private_pension: "Possui Previdência Privada",
    has_internet_banking: "Utiliza Internet Banking",
    has_token_installed: "Token de Segurança Instalado",
    has_traveled: "Já Viajou",

    // Probabilidades
    personal_credit_probability: "Probabilidade de Crédito Pessoal",
    vehicle_financing_probability: "Probabilidade de Financiamento de Veículos",
    internet_shopping_probability: "Probabilidade de Compras Online",
    multiple_cards_probability: "Probabilidade de Múltiplos Cartões",
    prime_card_probability: "Probabilidade de Cartão Prime",
    cable_tv_probability: "Probabilidade de TV por Assinatura",
    broadband_probability: "Probabilidade de Banda Larga",
    own_house_probability: "Probabilidade de Ter Casa Própria",
    prepaid_cell_probability: "Probabilidade de Celular Pré-Pago",
    postpaid_cell_probability: "Probabilidade de Celular Pós-Pago",
    real_estate_credit_probability: "Probabilidade de Crédito Imobiliário",
    auto_insurance_probability: "Probabilidade de Seguro Automotivo",
    health_insurance_probability: "Probabilidade de Plano de Saúde",
    life_insurance_probability: "Probabilidade de Seguro de Vida",
    home_insurance_probability: "Probabilidade de Seguro Residencial",
    investments_probability: "Probabilidade de Ter Investimentos",
    consigned_probability: "Probabilidade de Empréstimo Consignado",
    private_pension_probability: "Probabilidade de Previdência Privada",
    miles_redemption_probability: "Probabilidade de Resgate de Milhas",
    discount_hunter_probability: "Probabilidade de Ser Caçador de Descontos",
    fitness_probability: "Probabilidade de Estilo de Vida Fitness",
    tourism_probability: "Probabilidade de Interesse em Turismo",
    luxury_probability: "Probabilidade de Interesse em Luxo",
    cinephile_probability: "Probabilidade de Ser Cinéfilo",
    public_transport_probability: "Probabilidade de Uso de Transporte Público",
    online_games_probability: "Probabilidade de Interesse em Jogos Online",
    video_game_probability: "Probabilidade de Interesse em Video Games",
    early_adopters_probability: "Probabilidade de Ser um Inovador (Early Adopter)"
};

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
    if (valor === null || valor === undefined || valor === "" || valor === "0.00") {
        return `<p><strong>${label}:</strong> Não disponível</p>`;
    }
    return `<p><strong>${label}:</strong> ${valor}</p>`;
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
        if (!data || !data.dados || typeof data.dados !== "object") {
            console.log("Resposta inesperada:", data);
            throw new Error("Nenhuma informação encontrada para este CPF.");
        }
        const dados = data.dados;
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

        html += "<h3>Compras</h3>";
        if (dados.purchases?.length) {
            dados.purchases.forEach((compra) => {
            html += `<p><strong>Produto:</strong> ${compra.product || "N/A"} | <strong>Quantidade:</strong> ${compra.quantity || "1"} | <strong>Preço:</strong> R$ ${compra.price || "0,00"}</p>`;
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
        const interesses = dados.interests;
        if (interesses && Object.keys(interesses).length) {
            for (const chave in interesses) {
                const label = interessesLabels[chave] || chave;
                const valor = interesses[chave];

                let exibicao;

                if (typeof valor === "boolean") {
                    exibicao = valor ? "Sim" : "Não";
                } else if (typeof valor === "number") {
                    exibicao = valor.toFixed(0) + "%";
                } else if (valor === null || valor === undefined) {
                    exibicao = "Não disponível";
                } else {
                    exibicao = valor;
                }

                html += `<p><strong>${label}:</strong> ${exibicao}</p>`;
            }
        } else {
            html += "<p>Não disponível</p>";
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
async function baixarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const dados = {
        nome: document.getElementById('nome').innerText,
        cpf: document.getElementById('cpf_resultado').innerText,
        safra: document.getElementById('safra').innerText,
        nascimento: document.getElementById('nascimento').innerText,
        nome_mae: document.getElementById('nome_mae').innerText,
        sexo: document.getElementById('sexo').innerText,
        email: document.getElementById('email').innerText,
        obito: document.getElementById('obito').innerText,
        status_receita: document.getElementById('status_receita').innerText,
        cbo: document.getElementById('cbo').innerText,
        faixa_renda: document.getElementById('faixa_renda').innerText,
        veiculos: document.getElementById('veiculos').innerText,
        telefones: document.getElementById('telefones').innerText,
        celulares: document.getElementById('celulares').innerText,
        empregos: document.getElementById('empregos').innerText,
        enderecos: document.getElementById('enderecos').innerText,
    };

    doc.text("Relatório da Consulta CPF", 10, 10);

    let y = 20;
    for (const [chave, valor] of Object.entries(dados)) {
        doc.text(`${chave.charAt(0).toUpperCase() + chave.slice(1)}: ${valor}`, 10, y);
        y += 10;
    }

