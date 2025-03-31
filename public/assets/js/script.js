let captchaValidado = false;

function onCaptchaSuccess() {
  captchaValidado = true;
  // Garante que o botão tem o ID correto no HTML: id="consultarBtn"
  const consultarBtn = document.getElementById("consultarBtn");
  if (consultarBtn) {
    consultarBtn.disabled = false;
  } else {
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
  // --- ADICIONADO: Verificação do CAPTCHA ---
  if (!captchaValidado) {
    document.getElementById("resultado").innerText =
      "Por favor, resolva o CAPTCHA.";
    return;
  }

  const cpfInput = document.getElementById("cpf");
  const cpf = cpfInput.value;
  const resultadoElement = document.getElementById("resultado");
  const dadosElement = document.getElementById("dados");

  if (cpf.length < 14) {
    resultadoElement.innerText = "CPF inválido!";
    return;
  }

  resultadoElement.innerText = "Consultando...";
  dadosElement.style.display = "none"; // Esconde resultados anteriores

  // --- ALTERADO: URL agora aponta para o seu backend api.php ---
  const localApiUrl = "../backend/api.php"; // <-- Ajuste o caminho se necessário
  const cpfLimpo = cpf.replace(/\D/g, "");

  try {
    // --- ALTERADO: Fetch para api.php usando POST e enviando CPF no corpo ---
    const response = await fetch(localApiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ cpf: cpfLimpo }), // Envia o CPF limpo
    });

    // --- ALTERADO: Tratamento de erro mais detalhado ---
    if (!response.ok) {
      let errorMsg = `Erro na consulta (${response.status}).`;
      try {
        // Tenta pegar a mensagem de erro do seu api.php (que pode ser da API externa)
        const errorData = await response.json();
        // A resposta de erro do seu api.php tem a chave "message"
        if (errorData && errorData.message) {
          errorMsg = errorData.message;
        } else {
          // Se não conseguiu ler a mensagem JSON, usa o texto da resposta
          let errorText = await response.text();
          if (errorText)
            errorMsg += ` Resposta: ${errorText.substring(0, 100)}`; // Limita o tamanho
        }
      } catch (e) {
        let errorText = await response.text();
        if (errorText) errorMsg += ` Resposta: ${errorText.substring(0, 100)}`;
      }
      throw new Error(errorMsg);
    }

    // A resposta do seu api.php (se sucesso) é a resposta DIRETA da API externa
    const data = await response.json();

    // O restante da lógica para processar 'data' permanece o mesmo
    if (!data || !data.data) {
      // Verifica se 'data' existe e tem a propriedade 'data'
      throw new Error(
        "Nenhuma informação encontrada para este CPF ou resposta inválida."
      );
    }

    const dados = data.data;

    // Preenche os campos com os dados da API (lógica existente mantida)
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

    // Usando optional chaining (?) e nullish coalescing (??) para segurança
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

    const end = dados.endereco; // Acesso único para evitar repetição
    const enderecos = end
      ? `${end.tipo || ""} ${end.logradouro || "Não disponível"}, ${
          end.numero || "S/N"
        }, ${end.bairro || "Não disponível"}, ${
          end.cidade || "Não disponível"
        } - ${end.uf || ""}, CEP: ${end.cep || "Não disponível"}`
      : "Não disponível";
    document.getElementById("enderecos").innerHTML = enderecos; // Removido '|| "Não disponível"' redundante

    dadosElement.style.display = "block";
    resultadoElement.innerText = `Consulta realizada para o CPF: ${cpf}`;
  } catch (error) {
    console.error("Erro ao consultar CPF:", error); // Loga o erro detalhado no console
    resultadoElement.innerText = `Erro: ${error.message}`;
    dadosElement.style.display = "none"; // Esconde a área de dados em caso de erro
  }
}

// Função auxiliar para formatar CPF (usada para exibir o CPF retornado)
function formatarCPF(cpf) {
  if (!cpf) return "";
  const cpfLimpo = cpf.replace(/\D/g, "");
  if (cpfLimpo.length !== 11) return cpf; // Retorna original se não for formato esperado
  return cpfLimpo.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}
