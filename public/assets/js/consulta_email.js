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

function consultarEmail() {
  if (!captchaValidado) {
    document.getElementById("resultado").innerText =
      "Por favor, resolva o CAPTCHA.";
    return;
  }

  const consultarBtn = document.getElementById("consultarBtn");
  consultarBtn.disabled = true;

  const emailInput = document.getElementById("email");
  const email = emailInput.value;
  const resultadoElement = document.getElementById("resultado");
  const dadosElement = document.getElementById("dados");

  resultadoElement.innerText = "Consultando...";
  dadosElement.style.display = "none";

  const localApiUrl = "../backend/api_email.php";
  const emailLimpo = email.replace(/\s/g, "");

  fetch(localApiUrl, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email: emailLimpo }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Erro na consulta (${response.status}).`);
      }
      return response.json();
    })
    .then((resposta) => {
      const pessoas = resposta.dados;

      if (!Array.isArray(pessoas) || pessoas.length === 0) {
        resultadoElement.innerText = "Nenhum dado encontrado para o email informado.";
        return;
      }

      let html = `<h3>Resultado da consulta</h3>`;

      pessoas.forEach((pessoa, index) => {
        const endereco = pessoa.address || {};
        const emailData = pessoa.email_data || {};

        html += `
          <div style="margin-bottom: 16px; border: 1px solid #ccc; padding: 10px;">
            <h4>Pessoa ${index + 1}</h4>
            ${exibirCampo("Nome", pessoa.name)}
            ${exibirCampo("CPF", pessoa.cpf)}
            ${exibirCampo("RG", pessoa.rg)}
            ${exibirCampo("Nome da Mãe", pessoa.mother_name)}
            
            <br><strong>Endereço:</strong><br>
            ${exibirCampo("Tipo", endereco.type)}
            ${exibirCampo("Rua", endereco.street)}
            ${exibirCampo("Número", endereco.number)}
            ${exibirCampo("Complemento", endereco.complement)}
            ${exibirCampo("Bairro", endereco.neighborhood)}
            ${exibirCampo("Cidade", endereco.city)}
            ${exibirCampo("CEP", endereco.postal_code)}
            
            <br><strong>Informações de Email:</strong><br>
            ${exibirCampo("Email", emailData.email)}
            ${exibirCampo("Prioridade", emailData.priority)}
            ${exibirCampo("Score", emailData.score)}
            ${exibirCampo("Email Pessoal?", emailData.personal_email)}
            ${exibirCampo("Estrutura", emailData.structure)}
          </div>
        `;
      });

      dadosElement.innerHTML = html;
      dadosElement.style.display = "block";
      resultadoElement.innerText = `Consulta realizada para o Email: ${email}`;
      document.getElementById("acoes").style.display = "block";
    })
    .catch((error) => {
      console.error("Erro ao consultar Email:", error);
      resultadoElement.innerText = `Erro: ${error.message}`;
      dadosElement.style.display = "none";
    })
    .finally(() => {
      consultarBtn.disabled = false;
      resetCaptcha();
    });
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