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
    .then((pessoas) => {
      if (!Array.isArray(pessoas) || pessoas.length === 0) {
        throw new Error("Nenhum dado encontrado para este email.");
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
