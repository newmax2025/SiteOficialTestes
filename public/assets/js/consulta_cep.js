let captchaValidado = false;

function onCaptchaSuccess() {
  captchaValidado = true;
  document.getElementById("consultarBtn").disabled = false;
}

function resetCaptcha() {
  captchaValidado = false;
  document.getElementById("consultarBtn").disabled = true;

  setTimeout(() => {
    const captchaContainer = document.getElementById("captcha");
    if (captchaContainer) {
      captchaContainer.innerHTML = "";
      turnstile.render("#captcha", {
        sitekey: "0x4AAAAAABDPzCDp7OiEAfvh",
        callback: onCaptchaSuccess,
      });
    } else {
      console.warn("Elemento CAPTCHA não encontrado!");
    }
  }, 500);
}

function exibirCampo(label, valor) {
  if (
    valor === null ||
    valor === undefined ||
    valor === "" ||
    valor === "0.00"
  ) {
    return `<p><strong>${label}:</strong> Não disponível</p>`;
  }
  return `<p><strong>${label}:</strong> ${valor}</p>`;
}

function consultarCep() {
  if (!captchaValidado) {
    document.getElementById("resultado").innerText =
      "Por favor, resolva o CAPTCHA.";
    return;
  }

  const consultarBtn = document.getElementById("consultarBtn");
  consultarBtn.disabled = true;

  const cepInput = document.getElementById("cep");
  const cep = cepInput.value;
  const resultadoElement = document.getElementById("resultado");
  const dadosElement = document.getElementById("dados");

  if (cep.length < 8) {
    resultadoElement.innerText = "CEP inválido!";
    consultarBtn.disabled = false;
    return;
  }

  resultadoElement.innerText = "Consultando...";
  dadosElement.style.display = "none";

  const localApiUrl = "../backend/api_cep.php";
  const cepLimpo = cep.replace(/\D/g, "");

  fetch(localApiUrl, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ cep: cepLimpo }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Erro na consulta (${response.status}).`);
      }
      return response.json();
    })
    .then((data) => {
      if (
        !data.sucesso ||
        !Array.isArray(data.dados) ||
        data.dados.length === 0
      ) {
        throw new Error("Nenhuma informação encontrada para este CEP.");
      }

      let html = `<h3>Resultados encontrados: ${data.dados.length}</h3>`;

      data.dados.forEach((pessoa, index) => {
        const endereco = pessoa.endereco || {};
        html += `<div style="margin-bottom: 16px; border-bottom: 1px solid #ccc; padding-bottom: 8px;">
      <strong>Resultado ${index + 1}</strong><br>
      ${exibirCampo("Nome", pessoa.nome)}
      ${exibirCampo("CPF", pessoa.cpf)}
      ${exibirCampo("Nome da Mãe", pessoa.mae)}
      <br><strong>Endereço:</strong><br>
      ${exibirCampo("Rua", endereco.logradouro)}
      ${exibirCampo("Número", endereco.numero)}
      ${exibirCampo("Complemento", endereco.complemento)}
      ${exibirCampo("Bairro", endereco.bairro)}
      ${exibirCampo("CEP", endereco.cep)}
      ${exibirCampo("Cidade", endereco.cidade)}
      ${exibirCampo("Estado", endereco.estado)}
    </div>`;
      });

      dadosElement.innerHTML = html;
      dadosElement.style.display = "block";
      resultadoElement.innerText = `Consulta realizada para o CEP: ${cep}`;
      document.getElementById("acoes").style.display = "block";
    })

    .catch((error) => {
      console.error("Erro ao consultar CEP:", error);
      resultadoElement.innerText = `Erro: ${error.message}`;
      dadosElement.style.display = "none";
    })
    .finally(() => {
      consultarBtn.disabled = false;
      resetCaptcha();
    });
}

function formatarCep(cep) {
  const cepLimpo = cep.replace(/\D/g, "");
  return cepLimpo.replace(/(\d{5})(\d{3})/, "$1-$2");
}

function formatCep(input) {
  let value = input.value.replace(/\D/g, "");
  value = value.replace(/(\d{5})(\d)/, "$1-$2");
  input.value = value;
}

function copiarDados() {
  const dados = document.getElementById("dados").innerText;
  navigator.clipboard
    .writeText(dados)
    .then(() => {
      alert("Dados copiados para a área de transferência!");
    })
    .catch((err) => {
      alert("Erro ao copiar os dados: " + err);
    });
}

function baixarPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  const texto = document.getElementById("dados").innerText;

  const margem = 10;
  const larguraTexto = 180; // largura do texto dentro da página (210 - 2x margem)
  const alturaLinha = 7;
  const linhas = doc.splitTextToSize(texto, larguraTexto);

  let y = margem;

  for (let i = 0; i < linhas.length; i++) {
    if (y > 280) {
      // Altura máxima da página A4 (297mm) - margem inferior
      doc.addPage();
      y = margem;
    }
    doc.text(linhas[i], margem, y);
    y += alturaLinha;
  }

  doc.save("dados_cpf.pdf");
}

function baixarTXT() {
  const dados = document.getElementById("dados").innerText;
  const blob = new Blob([dados], { type: "text/plain;charset=utf-8" });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = "dados_cpf.txt";
  link.click();
}