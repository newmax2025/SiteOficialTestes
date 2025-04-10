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

function consultarTel() {
  if (!captchaValidado) {
    document.getElementById("resultado").innerText =
      "Por favor, resolva o CAPTCHA.";
    return;
  }

  const consultarBtn = document.getElementById("consultarBtn");
  consultarBtn.disabled = true;

  const telInput = document.getElementById("tel");
  const tel = telInput.value;
  const resultadoElement = document.getElementById("resultado");
  const dadosElement = document.getElementById("dados");

  if (tel.length < 12) {
    resultadoElement.innerText = "Telefone inválido!";
    consultarBtn.disabled = false;
    return;
  }

  resultadoElement.innerText = "Consultando...";
  dadosElement.style.display = "none";

  const localApiUrl = "../../backend/consultas/api_tel.php";
  const telLimpo = tel.replace(/\D/g, "");

  fetch(localApiUrl, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ tel: telLimpo }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Erro na consulta (${response.status}).`);
      }
      return response.json();
    })
    .then((data) => {
      if (data.erro) {
        throw new Error(data.erro);
      }

      if (!data.sucesso || !Array.isArray(data.dados)) {
        throw new Error("Nenhuma informação encontrada para este telefone.");
      }

      let html = `<h3>Resultados encontrados: ${data.dados.length}</h3>`;

      data.dados.forEach((pessoa, index) => {
        const endereco = pessoa.address || {};

        html += `<div style="margin-bottom: 16px; border-bottom: 1px solid #ccc; padding-bottom: 8px;">
          <strong>Resultado ${index + 1}</strong><br>
          ${exibirCampo("Nome", pessoa.name)}
          ${exibirCampo("CPF", pessoa.document)}
          ${exibirCampo("Telefone", pessoa.phone)}
          <br><strong>Endereço:</strong><br>
          ${exibirCampo("Rua", endereco.street)}
          ${exibirCampo("Número", endereco.number)}
          ${exibirCampo("Bairro", endereco.neighborhood)}
          ${exibirCampo("CEP", endereco.zip_code)}
          ${exibirCampo("Cidade", endereco.city)}
          ${exibirCampo("Estado", endereco.state)}
        </div>`;
      });

      dadosElement.innerHTML = html;
      dadosElement.style.display = "block";
      resultadoElement.innerText = `Consulta realizada para o telefone: ${tel}`;
      document.getElementById("acoes").style.display = "block";
    })
    .catch((error) => {
      console.error("Erro ao consultar telefone:", error);
      resultadoElement.innerText = `Erro: ${error.message}`;
      dadosElement.style.display = "none";
    })
    .finally(() => {
      consultarBtn.disabled = false;
      resetCaptcha();
    });
}


function formatarTelefone(tel) {
  const telLimpo = tel.replace(/\D/g, "");
  return telLimpo.replace(/(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
}

function formatTel(input) {
  let value = input.value.replace(/\D/g, "");
  value = value.replace(/^(\d{2})(\d)/g, "($1) $2");
  value = value.replace(/(\d{5})(\d)/, "$1-$2");

  input.value = value;
}
