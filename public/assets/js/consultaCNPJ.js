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

function consultarCNPJ() {
  if (!captchaValidado) {
    document.getElementById("resultado").innerText =
      "Por favor, resolva o CAPTCHA.";
    return;
  }

  const consultarBtn = document.getElementById("consultarBtn");
  consultarBtn.disabled = true;

  const cnpjInput = document.getElementById("cnpj");
  const cnpj = cnpjInput.value;
  const resultadoElement = document.getElementById("resultado");
  const dadosElement = document.getElementById("dados");

  if (cnpj.length < 12) {
    resultadoElement.innerText = "CNPJ inválido!";
    consultarBtn.disabled = false;
    return;
  }

  resultadoElement.innerText = "Consultando...";
  dadosElement.style.display = "none";

  const localApiUrl = "../backend/apiCNPJ.php";
  const cnpjLimpo = cnpj.replace(/\D/g, "");

  fetch(localApiUrl, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ cnpj: cnpjLimpo }),
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

      let empresa = data.dados;
      const endereco = empresa.address || {};
      const contato = empresa.contact || {};
      const atividades = empresa.activities || {};
      const socios = empresa.partners || [];

      let html = `<h3>Resultado da consulta</h3>`;

      html += `
        ${exibirCampo("CNPJ", empresa.document)}
        ${exibirCampo("Nome Empresarial", empresa.name)}
        ${exibirCampo("Nome Fantasia", empresa.trade_name)}
        ${exibirCampo("Tipo", empresa.type)}
        ${exibirCampo("Situação Cadastral", empresa.status)}
        ${exibirCampo("Data de Abertura", empresa.opening_date)}
        ${exibirCampo("Porte", empresa.size)}
        ${exibirCampo("Natureza Jurídica", empresa.legal_nature)}
        ${exibirCampo("Capital Social", `R$ ${empresa.capital}`)}
        ${exibirCampo("Telefone", contato.phone)}
        
        <br><strong>Endereço:</strong><br>
        ${exibirCampo("Rua", endereco.street)}
        ${exibirCampo("Número", endereco.number)}
        ${exibirCampo("Complemento", endereco.complement)}
        ${exibirCampo("Bairro", endereco.neighborhood)}
        ${exibirCampo("CEP", endereco.postal_code)}
        ${exibirCampo("Cidade", endereco.city)}
        ${exibirCampo("Estado", endereco.state)}
      `;

      if (atividades.secondary && atividades.secondary.length > 0) {
        html += `<br><strong>Atividades Secundárias:</strong><ul>`;
        atividades.secondary.forEach((atividade) => {
          html += `<li>${atividade.codigo} - ${atividade.descricao}</li>`;
        });
        html += `</ul>`;
      }

      if (socios.length > 0) {
        html += `<br><strong>Sócios:</strong><br>`;
        socios.forEach((socio, index) => {
          html += `
            <div style="margin-bottom: 8px; border: 1px solid #ccc; padding: 6px;">
              ${exibirCampo("Nome", socio.nome)}
              ${exibirCampo("CPF/CNPJ", socio.cpf_cnpj)}
              ${exibirCampo("Faixa Etária", socio.faixa_etaria)}
              ${exibirCampo("Qualificação", socio.qualificacao)}
              ${exibirCampo("Data de Entrada", socio.data_entrada)}
            </div>
          `;
        });
      }

      dadosElement.innerHTML = html;
      dadosElement.style.display = "block";
      resultadoElement.innerText = `Consulta realizada para o CNPJ: ${cnpj}`;
      document.getElementById("acoes").style.display = "block";
    })
    .catch((error) => {
      console.error("Erro ao consultar CNPJ:", error);
      resultadoElement.innerText = `Erro: ${error.message}`;
      dadosElement.style.display = "none";
    })
    .finally(() => {
      consultarBtn.disabled = false;
      resetCaptcha();
    });
}

function formatarCNPJ(cnpj) {
  const cnpjLimpo = cnpj.replace(/\D/g, "");
  return cnpjLimpo.replace(
    /^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/,
    "$1.$2.$3/$4-$5"
  );
}

function formatCNPJ(input) {
  let value = input.value.replace(/\D/g, "");
  value = value.replace(/^(\d{2})(\d)/, "$1.$2");
  value = value.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
  value = value.replace(/^(\d{2})\.(\d{3})\.(\d{3})(\d)/, "$1.$2.$3/$4");
  value = value.replace(
    /^(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})(\d)/,
    "$1.$2.$3/$4-$5"
  );

  input.value = value;
}
