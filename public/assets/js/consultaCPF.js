const inputCPF = document.getElementById("cpfInput");
const btnBuscar = document.getElementById("buscarBtn");
const resultado = document.getElementById("resultado");
const dadosContainer = document.getElementById("dados");
const downloadPDFBtn = document.getElementById("downloadPDFBtn");

btnBuscar.addEventListener("click", consultarCPF);

function consultarCPF() {
    const cpf = inputCPF.value.trim();

    if (cpf === "") {
        resultado.textContent = "Por favor, informe um CPF.";
        return;
    }

    resultado.textContent = "Buscando dados...";

    fetch(`https://api-publica.speedio.com.br/buscarcnpj?cnpj=${cpf}`)
        .then((response) => {
            if (!response.ok) {
                throw new Error("Erro na requisição");
            }
            return response.json();
        })
        .then((data) => {
            resultado.textContent = "";
            exibirDados(data);
            downloadPDFBtn.style.display = "inline-block"; // Mostra o botão PDF
        })
        .catch((error) => {
            resultado.textContent = "Erro ao buscar os dados. Verifique o CPF informado.";
            dadosContainer.innerHTML = "";
            downloadPDFBtn.style.display = "none"; // Esconde o botão PDF
            console.error("Erro:", error);
        });
}

function exibirDados(data) {
    const {
        NOME_EMPRESARIAL,
        NATUREZA_JURIDICA,
        DATA_ABERTURA,
        PORTE,
        CNPJ,
        STATUS,
        CNAE_PRINCIPAL_CODIGO,
        CNAE_PRINCIPAL_DESCRICAO,
        CNAE_SECUNDARIO,
        CEP,
        LOGRADOURO,
        NUMERO,
        COMPLEMENTO,
        BAIRRO,
        MUNICIPIO,
        UF,
        EMAIL,
        TELEFONE,
        CAPITAL_SOCIAL,
        QSA
    } = data;

    dadosContainer.innerHTML = `
        <p><strong>Nome Empresarial:</strong> ${NOME_EMPRESARIAL}</p>
        <p><strong>Natureza Jurídica:</strong> ${NATUREZA_JURIDICA}</p>
        <p><strong>Data de Abertura:</strong> ${DATA_ABERTURA}</p>
        <p><strong>Porte:</strong> ${PORTE}</p>
        <p><strong>CNPJ:</strong> ${CNPJ}</p>
        <p><strong>Status:</strong> ${STATUS}</p>
        <p><strong>CNAE Principal:</strong> ${CNAE_PRINCIPAL_CODIGO} - ${CNAE_PRINCIPAL_DESCRICAO}</p>
        <p><strong>CNAE Secundário:</strong> ${CNAE_SECUNDARIO}</p>
        <p><strong>Endereço:</strong> ${LOGRADOURO}, ${NUMERO}, ${COMPLEMENTO ? COMPLEMENTO + ', ' : ''}${BAIRRO}, ${MUNICIPIO} - ${UF}, ${CEP}</p>
        <p><strong>Email:</strong> ${EMAIL || 'Não informado'}</p>
        <p><strong>Telefone:</strong> ${TELEFONE || 'Não informado'}</p>
        <p><strong>Capital Social:</strong> R$ ${CAPITAL_SOCIAL}</p>
        <div><strong>Quadro Societário:</strong><br>${formatarQSA(QSA)}</div>
    `;
}

function formatarQSA(qsaArray) {
    if (!Array.isArray(qsaArray) || qsaArray.length === 0) {
        return "Não informado";
    }

    return qsaArray
        .map((socio) => `• ${socio.NOME} (${socio.QUAL})`)
        .join("<br>");
}

// Função para gerar o PDF
function gerarPDF() {
    const dadosElement = document.getElementById("dados");

    html2canvas(dadosElement, {
        scale: 2
    }).then(canvas => {
        const imgData = canvas.toDataURL("image/png");

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF("p", "mm", "a4");

        const pageWidth = pdf.internal.pageSize.getWidth();
        const imgProps = pdf.getImageProperties(imgData);
        const imgRatio = imgProps.width / imgProps.height;

        const pdfWidth = pageWidth;
        const pdfHeight = pageWidth / imgRatio;

        pdf.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
        pdf.save("consulta_cnpj.pdf");
    });
}
