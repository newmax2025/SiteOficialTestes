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

function consultarCPF() {
  if (!captchaValidado) {
    document.getElementById("resultado").innerText =
      "Por favor, resolva o CAPTCHA.";
    return;
  }

  const consultarBtn = document.getElementById("consultarBtn");
  consultarBtn.disabled = true;

  const cpf = document.getElementById("cpf").value.replace(/\D/g, "");
  const nome = document.getElementById("nome").value.trim(); // não usa mais toUpperCase

  const resultadoElement = document.getElementById("resultado");
  const dadosElement = document.getElementById("dados");

  resultadoElement.innerText = "Consultando...";
  dadosElement.style.display = "none";

  fetch("../backend/api_pix.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ cpf, nome }),
  })
    .then((response) => {
      if (!response.ok)
        throw new Error(`Erro na consulta (${response.status}).`);
      return response.json();
    })
    .then((data) => {
      if (!Array.isArray(data.dados) || data.dados.length === 0) {
        throw new Error("Nenhum resultado encontrado.");
      }

      let html = "<h3>Resultados encontrados:</h3>";
      data.dados.forEach((pessoa, index) => {
        const endereco = pessoa.address || {};
        html += `
      <div class="pessoa-card">
        <p><strong>Nome:</strong> ${pessoa.name || "Não disponível"}</p>
        <p><strong>CPF:</strong> ${
          formatarCPF(pessoa.cpf) || "Não disponível"
        }</p>
        <p><strong>Nascimento:</strong> ${
          pessoa.birth_date || "Não disponível"
        }</p>
        <p><strong>Sexo:</strong> ${
          pessoa.gender === "M"
            ? "Masculino"
            : pessoa.gender === "F"
            ? "Feminino"
            : "Não disponível"
        }</p>
        <p><strong>Mãe:</strong> ${pessoa.mother_name || "Não disponível"}</p>
        <p><strong>Renda Presumida:</strong> R$ ${
          pessoa.presumed_income || "Não disponível"
        }</p>
        <p><strong>Endereço:</strong><br>
          ${endereco.street || ""} ${endereco.number || ""}${
          endereco.complement ? ", " + endereco.complement : ""
        }<br>
          ${endereco.neighborhood || ""}${endereco.neighborhood ? ", " : ""}${
          endereco.city || ""
        } - ${endereco.state || endereco.uf || ""}<br>
          CEP: ${endereco.zip_code || "Não disponível"}
        </p>
      </div>
      <hr>
    `;
      });

      dadosElement.innerHTML = html;
      dadosElement.style.display = "block";
      resultadoElement.innerText = `Foram encontrados ${data.dados.length} resultado(s).`;
    })
    .catch((error) => {
      console.error("Erro ao consultar:", error);
      resultadoElement.innerText = `Erro: ${error.message}`;
      dadosElement.style.display = "none";
    })
    .finally(() => {
      consultarBtn.disabled = false;
      resetCaptcha();
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
