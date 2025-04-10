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

function exibirLista(label, lista) {
  if (!Array.isArray(lista) || lista.length === 0) {
    return `<p><strong>${label}:</strong> Nenhum encontrado</p>`;
  }

  let html = `<p><strong>${label}:</strong></p><ul>`;
  lista.forEach((item) => {
    html += `<li>${item}</li>`;
  });
  html += "</ul>";
  return html;
}

function consultarNome() {
  if (!captchaValidado) {
    document.getElementById("resultado").innerText =
      "Por favor, resolva o CAPTCHA.";
    return;
  }

  const consultarBtn = document.getElementById("consultarBtn");
  consultarBtn.disabled = true;

  const nomeInput = document.getElementById("nome");
  const nome = nomeInput.value;
  const resultadoElement = document.getElementById("resultado");
  const dadosElement = document.getElementById("dados");

  resultadoElement.innerText = "Consultando...";
  dadosElement.style.display = "none";

  const localApiUrl = "../../backend/api_nome.php";
  const nomeLimpo = nome.trim();

  fetch(localApiUrl, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ nome: nomeLimpo }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Erro na consulta (${response.status}).`);
      }
      return response.json();
    })
    .then((resposta) => {
      const pessoas = resposta.dados?.data || [];

      if (!Array.isArray(pessoas) || pessoas.length === 0) {
        resultadoElement.innerText =
          "Nenhum dado encontrado para o nome informado.";
        return;
      }

      let html = `<h3>Resultado da consulta</h3>`;

      pessoas.forEach((pessoa, index) => {
        const dados = pessoa.dados_basicos || {};
        const endereco = pessoa.endereco || {};
        const obito = dados.obito || {};

        html += `
          <div style="margin-bottom: 16px; border: 1px solid #ccc; padding: 10px;">
            <h4>Pessoa ${index + 1}</h4>
            ${exibirCampo("Nome", dados.nome)}
            ${exibirCampo("CPF", dados.cpf)}
            ${exibirCampo("Safra", dados.safra)}
            ${exibirCampo("Nascimento", dados.nascimento)}
            ${exibirCampo("Nome da Mãe", dados.nome_mae)}
            ${exibirCampo("Sexo", dados.sexo)}
            ${exibirCampo("Email", dados.email)}
            ${exibirCampo("Situação Receita Federal", dados.status_receita)}
            ${exibirCampo("CBO", dados.cbo)}
            ${exibirCampo("Renda Estimada", dados.faixa_renda)}
            ${exibirCampo("Qtde. Veículos", dados.qt_veiculos)}
            ${exibirCampo(
              "Participação Societária",
              dados.pct_cargo_societario
            )}

            <br><strong>Óbito:</strong><br>
            ${exibirCampo("Status de Óbito", obito.status)}
            ${exibirCampo("Data de Óbito", obito.data)}

            <br><strong>Endereço:</strong><br>
            ${exibirCampo("Tipo", endereco.tipo)}
            ${exibirCampo("Logradouro", endereco.logradouro)}
            ${exibirCampo("Número", endereco.numero)}
            ${exibirCampo("Complemento", endereco.complemento)}
            ${exibirCampo("Bairro", endereco.bairro)}
            ${exibirCampo("Cidade", endereco.cidade)}
            ${exibirCampo("Estado", endereco.estado)}
            ${exibirCampo("UF", endereco.uf)}
            ${exibirCampo("CEP", endereco.cep)}

            <br><strong>Telefones:</strong><br>
            ${exibirLista("Telefones", pessoa.telefones || [])}

            <br><strong>Celulares:</strong><br>
            ${exibirLista("Celulares", pessoa.celulares || [])}

            <br><strong>Veículos:</strong><br>
            ${
              pessoa.veiculos.length > 0
                ? pessoa.veiculos
                    .map(
                      (v, i) => `
                  <div style="margin-left: 16px;">
                    <p><strong>Veículo ${i + 1}</strong></p>
                    ${exibirCampo("Placa", v.placa)}
                    ${exibirCampo("Modelo", v.modelo)}
                    ${exibirCampo("Marca", v.marca)}
                    ${exibirCampo("Ano", v.ano)}
                    ${exibirCampo("UF", v.uf)}
                  </div>`
                    )
                    .join("")
                : "<p>Nenhum veículo encontrado.</p>"
            }
          </div>
        `;
      });

      dadosElement.innerHTML = html;
      dadosElement.style.display = "block";
      resultadoElement.innerText = `Consulta realizada para o nome: ${nome}`;
      document.getElementById("acoes").style.display = "block";
    })
    .catch((error) => {
      console.error("Erro ao consultar nome:", error);
      resultadoElement.innerText = `Erro: ${error.message}`;
      dadosElement.style.display = "none";
    })
    .finally(() => {
      consultarBtn.disabled = false;
      resetCaptcha();
    });
}
