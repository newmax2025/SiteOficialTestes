function formatarCPF(cpf) {
  return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}

async function consultarCPF() {
  let cpf = document.getElementById("cpf").value.trim();
  cpf = cpf.replace(/\D/g, ""); // Remove caracteres não numéricos

  if (cpf.length !== 11) {
    alert("Digite um CPF válido (11 dígitos)!");
    return;
  }

  document.getElementById("loading").classList.remove("hidden");
  document.getElementById("erro").classList.add("hidden");
  document.getElementById("tabela-resultados").style.display = "none";

  const TOKEN = "3cece996-29c9-40f7-94ab-198f008c3b17";
  const URL = `https://api.dbconsultas.com/api/v1/${TOKEN}/datalinkcpf/${cpf}`;

  try {
    const response = await fetch(URL);

    if (!response.ok) {
      throw new Error("Erro ao consultar CPF.");
    }

    const data = await response.json();
    document.getElementById("loading").classList.add("hidden");

    if (!data.data) {
      throw new Error("Nenhuma informação encontrada.");
    }

    const dados = data.data;
    const tabela = document.getElementById("dados");
    let enderecosHtml = "";
    let empregosHtml = "";

    if (Array.isArray(dados.endereco)) {
      dados.endereco.forEach((end, index) => {
        let numeroEndereco = dados.endereco.length > 1 ? `${index + 1} - ` : "";
        enderecosHtml += `<tr><th>Endereço</th><td>${numeroEndereco}${
          end.tipo || ""
        } ${end.logradouro || "Não disponível"}, ${end.numero || "S/N"}, ${
          end.bairro || "Não disponível"
        }, ${end.cidade || "Não disponível"} - ${end.uf || ""}, CEP: ${
          end.cep || "Não disponível"
        }</td></tr>`;
      });
    } else {
      enderecosHtml = `<tr><th>Endereço</th><td>${dados.endereco.tipo || ""} ${
        dados.endereco.logradouro || "Não disponível"
      }, ${dados.endereco.numero || "S/N"}, ${
        dados.endereco.bairro || "Não disponível"
      }, ${dados.endereco.cidade || "Não disponível"} - ${
        dados.endereco.uf || ""
      }, CEP: ${dados.endereco.cep || "Não disponível"}</td></tr>`;
    }

    if (Array.isArray(dados.empregos)) {
      dados.empregos.forEach((emp) => {
        empregosHtml += `<tr><th>Emprego</th><td>${
          emp.nome_empregador || "Não disponível"
        } (${emp.setor || "Não disponível"}) - ${
          emp.status || "Não disponível"
        }, Remuneração: ${emp.remuneracao || "Não disponível"}</td></tr>`;
      });
    } else {
      empregosHtml = `<tr><th>Emprego</th><td>Não disponível</td></tr>`;
    }

    tabela.innerHTML = `
            <tr><th>Nome</th><td>${
              dados.dados_basicos.nome || "Não disponível"
            }</td></tr>
            <tr><th>CPF</th><td>${formatarCPF(
              dados.dados_basicos.cpf
            )}</td></tr>
            <tr><th>Safra</th><td>${
              dados.dados_basicos.safra || "Não disponível"
            }</td></tr>
            <tr><th>Data de Nascimento</th><td>${
              dados.dados_basicos.nascimento || "Não disponível"
            }</td></tr>
            <tr><th>Nome da Mãe</th><td>${
              dados.dados_basicos.nome_mae || "Não disponível"
            }</td></tr>
            <tr><th>Sexo</th><td>${
              dados.dados_basicos.sexo === "M" ? "Masculino" : "Feminino"
            }</td></tr>
            <tr><th>Email</th><td>${
              dados.dados_basicos.email || "Não disponível"
            }</td></tr>
            <tr><th>Óbito</th><td>${
              dados.dados_basicos.obito?.status || "Não disponível"
            }</td></tr>
            <tr><th>Status Receita</th><td>${
              dados.dados_basicos.status_receita || "Não disponível"
            }</td></tr>
            <tr><th>CBO</th><td>${
              dados.dados_basicos.cbo || "Não disponível"
            }</td></tr>
            <tr><th>Faixa de Renda</th><td>${
              dados.dados_basicos.faixa_renda || "Não disponível"
            }</td></tr>
            ${enderecosHtml}
            ${empregosHtml}
            <tr><th>Veículos</th><td>${
              dados.veiculos.length > 0
                ? dados.veiculos.join(", ")
                : "Não disponível"
            }</td></tr>
            <tr><th>Telefones</th><td>${
              dados.telefones.length > 0
                ? dados.telefones.join(", ")
                : "Não disponível"
            }</td></tr>
            <tr><th>Celulares</th><td>${
              dados.celulares.length > 0
                ? dados.celulares.join(", ")
                : "Não disponível"
            }</td></tr>
        `;

    document.getElementById("tabela-resultados").style.display = "table";
  } catch (error) {
    document.getElementById("erro").innerText = error.message;
    document.getElementById("erro").classList.remove("hidden");
    document.getElementById("loading").classList.add("hidden");
  }
}
