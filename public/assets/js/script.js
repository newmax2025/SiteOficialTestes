let captchaValidado = false;

function onCaptchaSuccess() {
  captchaValidado = true;
  // Garante que o botão tem o ID correto no HTML: id="consultarBtn"
  const consultarBtn = document.getElementById("consultarBtn");
  if (consultarBtn) {
    consultarBtn.disabled = false;
  } else {
    // Log de erro se o botão não for encontrado (como visto no seu console)
    console.error("Botão com id 'consultarBtn' não encontrado.");
  }
}

function formatCPF(input) {
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

  // Obter o valor do token Turnstile
  const turnstileInput = document.querySelector(
    '[name="cf-turnstile-response"]'
  );
  const turnstileToken = turnstileInput ? turnstileInput.value : null;

  // --- DEBUGGING ADICIONADO (Passo 4) ---
  console.log("Tentando consultar CPF...");
  console.log("Elemento Turnstile Input:", turnstileInput); // Verifica se o elemento foi encontrado
  console.log("Valor do Token Turnstile:", turnstileToken); // Verifica qual valor foi pego (deve ser uma string longa se funcionou)
  // --- FIM DEBUGGING ---

  // Verifica se o token foi obtido
  if (!turnstileToken) {
    // Mensagem de erro mais informativa, sugerindo verificar o console
    resultadoElement.innerText =
      "Erro: Não foi possível obter o token CAPTCHA. Verifique o console (F12).";
    return; // Para a execução aqui
  }

  // Validação do formato do CPF
  if (cpf.length < 14) {
    resultadoElement.innerText = "CPF inválido!";
    return;
  }

  resultadoElement.innerText = "Consultando...";
  dadosElement.style.display = "none"; // Esconde resultados anteriores

  const localApiUrl = "../backend/api.php"; // Ajuste o caminho se necessário
  const cpfLimpo = cpf.replace(/\D/g, "");

  try {
    const response = await fetch(localApiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      // Envia CPF e Token CAPTCHA
      body: JSON.stringify({
        cpf: cpfLimpo,
        captchaToken: turnstileToken,
      }),
    });

    // Tratamento de erro da resposta (corrigido anteriormente)
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

    // Preenche os campos (Manter todo o preenchimento dos seus campos aqui)
    document.getElementById("nome").innerText =
      dados.dados_basicos?.nome || "Não disponível";
    document.getElementById("cpf_resultado").innerText =
      formatarCPF(dados.dados_basicos?.cpf || "") || "Não disponível";
    document.getElementById("safra").innerText =
      dados.dados_basicos?.safra || "Não disponível";
    document.getElementById("nascimento").innerText =
      dados.dados_basicos?.nascimento || "Não disponível";
    document.getElementById("nome_mae").innerText =
      dados.dados_basicos?.nome_mae || "Não disponível";
    document.getElementById("sexo").innerText =
      dados.dados_basicos?.sexo === "M"
        ? "Masculino"
        : dados.dados_basicos?.sexo === "F"
        ? "Feminino"
        : "Não disponível";
    document.getElementById("email").innerText =
      dados.dados_basicos?.email || "Não disponível";
    document.getElementById("obito").innerText =
      dados.dados_basicos?.obito?.status || "Não disponível";
    document.getElementById("status_receita").innerText =
      dados.dados_basicos?.status_receita || "Não disponível";
    document.getElementById("cbo").innerText =
      dados.dados_basicos?.cbo || "Não disponível";
    document.getElementById("faixa_renda").innerText =
      dados.dados_basicos?.faixa_renda || "Não disponível";
    document.getElementById("veiculos").innerText =
      dados.veiculos?.length > 0 ? dados.veiculos.join(", ") : "Não disponível";
    document.getElementById("telefones").innerText =
      dados.telefones?.length > 0
        ? dados.telefones.join(", ")
        : "Não disponível";
    document.getElementById("celulares").innerText =
      dados.celulares?.length > 0
        ? dados.celulares.join(", ")
        : "Não disponível";
    const empregos =
      dados.empregos
        ?.map(
          (emplo) => `${emplo.nome_empregador || "?"} (${emplo.setor || "?"})`
        )
        .join("<br>") ?? "";
    document.getElementById("empregos").innerHTML =
      empregos || "Não disponível";
    const end = dados.endereco;
    const enderecos = end
      ? `${end.tipo || ""} ${end.logradouro || "Não disponível"}, ${
          end.numero || "S/N"
        }, ${end.bairro || "Não disponível"}, ${
          end.cidade || "Não disponível"
        } - ${end.uf || ""}, CEP: ${end.cep || "Não disponível"}`
      : "Não disponível";
    document.getElementById("enderecos").innerHTML = enderecos;

    // Exibe os dados e mensagem de sucesso
    dadosElement.style.display = "block";
    resultadoElement.innerText = `Consulta realizada para o CPF: ${cpf}`;
  } catch (error) {
    // Captura e exibe erros
    console.error("Erro ao consultar CPF:", error); // Log detalhado no console
    resultadoElement.innerText = `Erro: ${error.message}`; // Mensagem para o usuário
    dadosElement.style.display = "none"; // Esconde a área de dados
  } finally {
    // Tenta resetar o CAPTCHA após cada tentativa
    try {
      if (window.turnstile) {
        window.turnstile.reset(); // Reseta o widget Turnstile
        captchaValidado = false; // Marca como não validado para a próxima consulta
        const consultarBtn = document.getElementById("consultarBtn");
        if (consultarBtn) {
          consultarBtn.disabled = true; // Desabilita o botão novamente
        }
      }
    } catch (e) {
      console.error("Erro ao resetar Turnstile:", e);
    }
  }
}

// Função auxiliar para formatar CPF (para exibição)
function formatarCPF(cpf) {
  if (!cpf) return "";
  const cpfLimpo = cpf.replace(/\D/g, "");
  if (cpfLimpo.length !== 11) return cpf; // Retorna original se não for formato esperado
  return cpfLimpo.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}
