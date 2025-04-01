let captchaValidado = false;

function onCaptchaSuccess(token) {
  captchaToken = token;
  const consultarBtn = document.getElementById("consultarBtn");
  consultarBtn.disabled = false;
}

function resetCaptcha() {
  captchaValidado = false; // Reseta a variável
  captchaToken = null; // Reseta o token armazenado

  if (typeof turnstile !== "undefined") {
    turnstile.reset(); // Reseta o CAPTCHA corretamente
  } else {
    console.error("Turnstile não carregado corretamente.");
  }

  document.getElementById("consultarBtn").disabled = true; // Desabilita o botão até novo CAPTCHA
}


function formatCPF(input) {
  let value = input.value.replace(/\D/g, "");
  value = value.replace(/(\d{3})(\d)/, "$1.$2");
  value = value.replace(/(\d{3})(\d)/, "$1.$2");
  value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
  input.value = value;
}

async function consultarCPF() {
  const consultarBtn = document.getElementById("consultarBtn");
  consultarBtn.disabled = true;

  const cpfInput = document.getElementById("cpf");
  const cpf = cpfInput.value;
  const resultadoElement = document.getElementById("resultado");
  const dadosElement = document.getElementById("dados");

  if (cpf.length < 14) {
    resultadoElement.innerText = "CPF inválido!";
    consultarBtn.disabled = false;
    return;
  }

  resultadoElement.innerText = "Validando CAPTCHA...";
  const turnstileResponse = document.querySelector(
    'input[name="cf-turnstile-response"]'
  ).value;

  if (!turnstileResponse) {
    resultadoElement.innerText = "Por favor, resolva o CAPTCHA.";
    consultarBtn.disabled = false;
    return;
  }

  // Primeiro, verificar se o Turnstile é válido
  try {
    const captchaResponse = await fetch("../backend/verificar_turnstile.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `token=${encodeURIComponent(turnstileResponse)}`,
    });

    const captchaData = await captchaResponse.json();

    if (!captchaData.success) {
      resultadoElement.innerText =
        "Falha na validação do CAPTCHA. Tente novamente.";
      resetCaptcha();
      consultarBtn.disabled = false;
      return;
    }
  } catch (error) {
    resultadoElement.innerText = "Erro ao validar CAPTCHA.";
    console.error("Erro na verificação do Turnstile:", error);
    resetCaptcha();
    consultarBtn.disabled = false;
    return;
  }

  resultadoElement.innerText = "Consultando CPF...";

  // Envia a requisição para API apenas se o CAPTCHA foi validado
  try {
    const response = await fetch("../backend/api.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ cpf: cpf.replace(/\D/g, "") }),
    });

    if (!response.ok) {
      throw new Error(`Erro ao consultar CPF. (${response.status})`);
    }

    const data = await response.json();

    if (!data || !data.data) {
      throw new Error("Nenhuma informação encontrada.");
    }

    // Exibe os dados retornados (mantendo a lógica existente)
    document.getElementById("nome").innerText =
      data.data.dados_basicos?.nome || "Não disponível";
    document.getElementById("cpf_resultado").innerText = formatarCPF(
      data.data.dados_basicos?.cpf || ""
    );
    document.getElementById("safra").innerText =
      data.data.dados_basicos?.safra || "Não disponível";
    document.getElementById("nascimento").innerText =
      data.data.dados_basicos?.nascimento || "Não disponível";
    document.getElementById("nome_mae").innerText =
      data.data.dados_basicos?.nome_mae || "Não disponível";

    dadosElement.style.display = "block";
    resultadoElement.innerText = `Consulta realizada para o CPF: ${cpf}`;
  } catch (error) {
    console.error("Erro ao consultar CPF:", error);
    resultadoElement.innerText = `Erro: ${error.message}`;
    dadosElement.style.display = "none";
  } finally {
    consultarBtn.disabled = false;
    
  }
  resetCaptcha();
}

// Corrige o reset do CAPTCHA corretamente
function resetCaptcha() {
  turnstile.reset();
}


// Função auxiliar para formatar CPF (usada para exibir o CPF retornado)
function formatarCPF(cpf) {
  if (!cpf) return "";
  const cpfLimpo = cpf.replace(/\D/g, "");
  if (cpfLimpo.length !== 11) return cpf; // Retorna original se não for formato esperado
  return cpfLimpo.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}
