let captchaValidado = false;
const consultarBtn = document.getElementById("consultarBtn");
const resultadoElement = document.getElementById("resultado");

async function onCaptchaSuccess(token) {
  if (resultadoElement) {
    resultadoElement.innerText = "Verificando CAPTCHA...";
  }
  if (consultarBtn) {
    consultarBtn.disabled = true;
  }
  captchaValidado = false;

  const verificarUrl = "../backend/verificar_turnstile.php";

  try {
    const formData = new FormData();
    formData.append("token", token);

    const response = await fetch(verificarUrl, {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      let errorMsg = `Erro ao verificar CAPTCHA (${response.status}).`;
      try {
        const errorBody = await response.text();
        try {
          const errorJson = JSON.parse(errorBody);
          if (errorJson && errorJson.error) {
            errorMsg += ` Detalhes: ${errorJson.error}`;
          } else if (errorBody) {
            errorMsg += ` Resposta: ${errorBody.substring(0, 100)}`;
          }
        } catch (e) {
          if (errorBody) {
            errorMsg += ` Resposta: ${errorBody.substring(0, 100)}`;
          }
        }
      } catch (readError) {
        errorMsg += " Não foi possível ler a resposta do servidor.";
      }
      throw new Error(errorMsg);
    }

    const data = await response.json();

    if (data.success === true) {
      captchaValidado = true;
      if (consultarBtn) {
        consultarBtn.disabled = false;
      }
      if (resultadoElement) {
        resultadoElement.innerText = "CAPTCHA verificado. Pode consultar.";
      }
      console.log("Verificação do Turnstile bem-sucedida no backend.");
    } else {
      throw new Error(
        data.error || "Falha na verificação do CAPTCHA pelo servidor."
      );
    }
  } catch (error) {
    console.error("Erro na verificação do Turnstile:", error);
    captchaValidado = false;
    if (consultarBtn) {
      consultarBtn.disabled = true;
    }
    if (resultadoElement) {
      resultadoElement.innerText = `Erro CAPTCHA: ${error.message}`;
    }
    if (typeof turnstile !== 'undefined') {
        turnstile.reset('#captcha');
      }
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
  if (!captchaValidado) {
    resultadoElement.innerText = "Por favor, resolva o CAPTCHA corretamente.";
    return;
  }

  const currentConsultarBtn = document.getElementById("consultarBtn");
  const currentResultadoElement = document.getElementById("resultado");
  const currentDadosElement = document.getElementById("dados");
  const cpfInput = document.getElementById("cpf");

  currentConsultarBtn.disabled = true;

  const cpf = cpfInput.value;

  if (cpf.length < 14) {
    currentResultadoElement.innerText = "CPF inválido!";
    currentConsultarBtn.disabled = false;
    return;
  }

  currentResultadoElement.innerText = "Consultando CPF...";
  currentDadosElement.style.display = "none";

  const localApiUrl = "../backend/api.php";
  const cpfLimpo = cpf.replace(/\D/g, "");

  try {
    const response = await fetch(localApiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ cpf: cpfLimpo }),
    });

    if (!response.ok) {
      let errorMsg = `Erro na consulta CPF (${response.status}).`;
      let responseBodyText = "";

      try {
        responseBodyText = await response.text();
        let errorData = null;
        try {
          errorData = JSON.parse(responseBodyText);
        } catch (parseError) {
          console.warn(
            "A resposta de erro do servidor (api.php) não era JSON:",
            parseError
          );
        }

        if (errorData && errorData.message) {
          errorMsg = errorData.message;
        } else if (responseBodyText) {
          errorMsg += ` Resposta: ${responseBodyText.substring(0, 150)}`;
        }
      } catch (readError) {
        console.error(
          "Não foi possível ler o corpo da resposta de erro (api.php):",
          readError
        );
        errorMsg += " (Não foi possível ler a resposta do servidor).";
      }
      throw new Error(errorMsg);
    }

    const data = await response.json();

    if (!data || !data.data) {
      throw new Error(
        "Nenhuma informação encontrada para este CPF ou resposta inválida (api.php)."
      );
    }

    const dados = data.data;

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
        ?.map((e) => `${e.nome_empregador || "?"} (${e.setor || "?"})`)
        .join("<br>") ?? "";
    document.getElementById("empregos").innerHTML =
      empregos || "Não disponível";
    const end = dados.endereco;
    const enderecos = end
      ? `${end.tipo || ""} ${end.logradouro || "Não disp."}, ${
          end.numero || "S/N"
        }, ${end.bairro || "Não disp."}, ${end.cidade || "Não disp."} - ${
          end.uf || ""
        }, CEP: ${end.cep || "Não disp."}`
      : "Não disponível";
    document.getElementById("enderecos").innerHTML = enderecos;

    currentDadosElement.style.display = "block";
    currentResultadoElement.innerText = `Consulta realizada para o CPF: ${cpf}`;
  } catch (error) {
    console.error("Erro ao consultar CPF (api.php):", error);
    currentResultadoElement.innerText = `Erro Consulta: ${error.message}`;
    currentDadosElement.style.display = "none";
  } finally {
    currentConsultarBtn.disabled = false;
  }
}

function formatarCPF(cpf) {
  if (!cpf) return "";
  const cpfLimpo = cpf.replace(/\D/g, "");
  if (cpfLimpo.length !== 11) return cpf;
  return cpfLimpo.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}

document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("consultarBtn");
  if (btn) {
    btn.disabled = true;
  }
});
