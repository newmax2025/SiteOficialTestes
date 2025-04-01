let captchaValidado = false;
let captchaToken = null;

function onCaptchaSuccess(token) {
  captchaValidado = true;
  captchaToken = token;
  document.getElementById("consultarBtn").disabled = false;
}

function resetCaptcha() {
  captchaValidado = false;
  captchaToken = null;

  if (typeof turnstile !== "undefined") {
    turnstile.reset();
  } else {
    console.error("Turnstile não carregado corretamente.");
  }

  document.getElementById("consultarBtn").disabled = true;
}

function formatarCPF(cpf) {
  if (!cpf) return "";
  return cpf
    .replace(/(\d{3})(\d)/, "$1.$2")
    .replace(/(\d{3})(\d)/, "$1.$2")
    .replace(/(\d{3})(\d{1,2})$/, "$1-$2");
}

function formatCPF(input) {
  let value = input.value.replace(/\D/g, "");
  input.value = formatarCPF(value);
}

async function consultarCPF() {
  if (!captchaValidado) {
    document.getElementById("resultado").innerText =
      "Por favor, resolva o CAPTCHA.";
    return;
  }

  const consultarBtn = document.getElementById("consultarBtn");
  consultarBtn.disabled = true;

  const cpfInput = document.getElementById("cpf");
  const cpf = cpfInput.value.replace(/\D/g, "");
  const resultadoElement = document.getElementById("resultado");
  const dadosElement = document.getElementById("dados");

  if (cpf.length !== 11) {
    resultadoElement.innerText = "CPF inválido!";
    return;
  }

  resultadoElement.innerText = "Consultando...";
  dadosElement.style.display = "none";

  const localApiUrl = "../backend/api.php";

  try {
    const response = await fetch(localApiUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ cpf: cpf }),
    });

    if (!response.ok) throw new Error(`Erro na consulta (${response.status})`);

    const data = await response.json();

    if (!data || !data.data)
      throw new Error("Nenhuma informação encontrada para este CPF.");

    const dados = data.data;

    document.getElementById("nome").innerText = dados.nome || "Não disponível";
    document.getElementById("cpf_resultado").innerText = formatarCPF(
      dados.cpf || ""
    );
    document.getElementById("safra").innerText =
      dados.safra || "Não disponível";
    document.getElementById("nascimento").innerText =
      dados.nascimento || "Não disponível";
    document.getElementById("nome_mae").innerText =
      dados.nome_mae || "Não disponível";
    document.getElementById("sexo").innerText =
      dados.sexo === "M" ? "Masculino" : "Feminino";
    document.getElementById("email").innerText =
      dados.email || "Não disponível";
    document.getElementById("obito").innerText =
      dados.obito || "Não disponível";
    document.getElementById("status_receita").innerText =
      dados.status_receita || "Não disponível";
    document.getElementById("cbo").innerText = dados.cbo || "Não disponível";
    document.getElementById("faixa_renda").innerText =
      dados.faixa_renda || "Não disponível";
    document.getElementById("veiculos").innerText =
      dados.veiculos || "Não disponível";
    document.getElementById("telefones").innerText =
      dados.telefones || "Não disponível";
    document.getElementById("celulares").innerText =
      dados.celulares || "Não disponível";
    document.getElementById("empregos").innerText =
      dados.empregos || "Não disponível";
    document.getElementById("enderecos").innerText =
      dados.enderecos || "Não disponível";

    dadosElement.style.display = "block";
    resultadoElement.innerText = `Consulta realizada para o CPF: ${formatarCPF(
      cpf
    )}`;
  } catch (error) {
    resultadoElement.innerText = `Erro: ${error.message}`;
  } finally {
    consultarBtn.disabled = false;
    resetCaptcha();
  }
}
