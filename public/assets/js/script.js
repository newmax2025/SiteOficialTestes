let captchaValidado = false;

function onCaptchaSuccess() {
  captchaValidado = true;
  const consultarBtn = document.getElementById("consultarBtn");
  if (consultarBtn) {
    consultarBtn.disabled = false;
  } else {
    console.error("Botão com id 'consultarBtn' não encontrado.");
  }
}

function formatCPF(input) {
  // ... (código existente)
  let value = input.value.replace(/\D/g, "");
  value = value.replace(/(\d{3})(\d)/, "$1.$2");
  value = value.replace(/(\d{3})(\d)/, "$1.$2");
  value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
  input.value = value;
}

async function consultarCPF() {
  // Verifica CAPTCHA antes de tudo
  if (!captchaValidado) {
    document.getElementById("resultado").innerText =
      "Por favor, resolva o CAPTCHA.";
    return;
  }

  const cpfInput = document.getElementById("cpf");
  const cpf = cpfInput.value;
  const resultadoElement = document.getElementById("resultado");
  const dadosElement = document.getElementById("dados");

  // --- NOVO: Obter o valor do token Turnstile ---
  const turnstileInput = document.querySelector(
    '[name="cf-turnstile-response"]'
  );
  const turnstileToken = turnstileInput ? turnstileInput.value : null;

  if (!turnstileToken) {
    resultadoElement.innerText =
      "Erro: Não foi possível obter o token CAPTCHA. Recarregue a página.";
    // Opcionalmente, resetar o widget Turnstile se a API permitir
    return;
  }
  // --- FIM NOVO ---

  if (cpf.length < 14) {
    resultadoElement.innerText = "CPF inválido!";
    return;
  }

  resultadoElement.innerText = "Consultando...";
  dadosElement.style.display = "none";

  const localApiUrl = "../backend/api.php"; // Ajuste o caminho se necessário
  const cpfLimpo = cpf.replace(/\D/g, "");

  try {
    const response = await fetch(localApiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      // --- ALTERADO: Envia CPF e Token CAPTCHA ---
      body: JSON.stringify({
        cpf: cpfLimpo,
        captchaToken: turnstileToken, // Envia o token obtido
      }),
      // --- FIM ALTERADO ---
    });

    // Bloco de tratamento de erro (já corrigido para evitar 'body already read')
    if (!response.ok) {
      let errorMsg = `Erro na consulta (${response.status}).`;
      let responseBodyText = "";
      try {
        responseBodyText = await response.text();
        let errorData = null;
        try {
          errorData = JSON.parse(responseBodyText);
        } catch (parseError) {
          /* ignore */
        }
        if (errorData && errorData.message) {
          errorMsg = errorData.message;
        } else if (responseBodyText) {
          errorMsg += ` Resposta: ${responseBodyText.substring(0, 150)}`;
        }
      } catch (readError) {
        errorMsg += " (Não foi possível ler a resposta do servidor).";
      }
      throw new Error(errorMsg);
    }

    const data = await response.json();

    // Lógica para exibir os dados (existente)
    if (!data || !data.data) {
      throw new Error(
        "Nenhuma informação encontrada para este CPF ou resposta inválida."
      );
    }
    const dados = data.data;
    // ... (resto do código para preencher os spans) ...
    document.getElementById("nome").innerText =
      dados.dados_basicos?.nome || "Não disponível";
    document.getElementById("cpf_resultado").innerText =
      formatarCPF(dados.dados_basicos?.cpf || "") || "Não disponível";
    document.getElementById("safra").innerText =
      dados.dados_basicos?.safra || "Não disponível";
    // [...] // Manter todo o preenchimento dos campos

    dadosElement.style.display = "block";
    resultadoElement.innerText = `Consulta realizada para o CPF: ${cpf}`;
  } catch (error) {
    console.error("Erro ao consultar CPF:", error);
    resultadoElement.innerText = `Erro: ${error.message}`;
    dadosElement.style.display = "none";
  } finally {
    // --- NOVO: Resetar CAPTCHA após tentativa (opcional mas recomendado) ---
    // Isso força o usuário a resolver novamente para a próxima consulta
    try {
      if (window.turnstile) {
        window.turnstile.reset(); // Reseta o widget Turnstile
        captchaValidado = false; // Marca como não validado
        const consultarBtn = document.getElementById("consultarBtn");
        if (consultarBtn) {
          consultarBtn.disabled = true; // Desabilita o botão novamente
        }
      }
    } catch (e) {
      console.error("Erro ao resetar Turnstile:", e);
    }
    // --- FIM NOVO ---
  }
}

// Função auxiliar para formatar CPF (existente)
function formatarCPF(cpf) {
  if (!cpf) return "";
  const cpfLimpo = cpf.replace(/\D/g, "");
  if (cpfLimpo.length !== 11) return cpf;
  return cpfLimpo.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}
