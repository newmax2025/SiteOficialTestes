function formatCPF(input) {
  let value = input.value.replace(/\D/g, "");
  value = value.replace(/(\d{3})(\d)/, "$1.$2");
  value = value.replace(/(\d{3})(\d)/, "$1.$2");
  value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
  input.value = value;
}

async function consultarCPF() {
  const cpf = document.getElementById("cpf").value;
  if (cpf.length < 14) {
    document.getElementById("resultado").innerText = "CPF inválido!";
    return;
  }

  document.getElementById("resultado").innerText = "Consultando...";

  const TOKEN = "3cece996-29c9-40f7-94ab-198f008c3b17"; // Substitua pelo seu token real
  const URL = `https://api.dbconsultas.com/api/v1/${TOKEN}/datalinkcpf/${cpf.replace(
    /\D/g,
    ""
  )}`;

  try {
    const response = await fetch(URL);
    if (!response.ok) {
      throw new Error("Erro ao consultar CPF.");
    }

    const data = await response.json();

    if (!data.data) {
      throw new Error("Nenhuma informação encontrada para este CPF.");
    }

    const dados = data.data;

    // Preenche os campos com os dados da API
    document.getElementById("nome").innerText =
      dados.dados_basicos.nome || "Não disponível";
    document.getElementById("cpf_resultado").innerText =
      formatarCPF(dados.dados_basicos.cpf) || "Não disponível";
    document.getElementById("safra").innerText =
      dados.dados_basicos.safra || "Não disponível";
    document.getElementById("nascimento").innerText =
      dados.dados_basicos.nascimento || "Não disponível";
    document.getElementById("nome_mae").innerText =
      dados.dados_basicos.nome_mae || "Não disponível";
    document.getElementById("sexo").innerText =
      dados.dados_basicos.sexo === "M"
        ? "Masculino"
        : "Feminino" || "Não disponível";
    document.getElementById("email").innerText =
      dados.dados_basicos.email || "Não disponível";
    document.getElementById("obito").innerText =
      dados.dados_basicos.obito?.status || "Não disponível";
    document.getElementById("status_receita").innerText =
      dados.dados_basicos.status_receita || "Não disponível";
    document.getElementById("cbo").innerText =
      dados.dados_basicos.cbo || "Não disponível";
    document.getElementById("faixa_renda").innerText =
      dados.dados_basicos.faixa_renda || "Não disponível";
    document.getElementById("veiculos").innerText =
      dados.veiculos.length > 0 ? dados.veiculos.join(", ") : "Não disponível";
    document.getElementById("telefones").innerText =
      dados.telefones.length > 0
        ? dados.telefones.join(", ")
        : "Não disponível";
    document.getElementById("celulares").innerText =
      dados.celulares.length > 0
        ? dados.celulares.join(", ")
        : "Não disponível";

    // Exibe todos os empregos como uma lista, cada emprego em uma linha
    const empregos = dados.empregos
      .map((emplo) => `${emplo.nome_empregador} (${emplo.setor})`)
      .join("<br>");
    document.getElementById("empregos").innerHTML =
      empregos || "Não disponível";

    // Exibe todos os endereços como uma lista, cada endereço em uma linha
    const enderecos = dados.endereco
      ? `${dados.endereco.tipo || ""} ${
          dados.endereco.logradouro || "Não disponível"
        }, ${dados.endereco.numero || "S/N"}, ${
          dados.endereco.bairro || "Não disponível"
        }, ${dados.endereco.cidade || "Não disponível"} - ${
          dados.endereco.uf || ""
        }, CEP: ${dados.endereco.cep || "Não disponível"}`
      : "Não disponível";
    document.getElementById("enderecos").innerHTML =
      enderecos || "Não disponível";

    document.getElementById("dados").style.display = "block";
    document.getElementById(
      "resultado"
    ).innerText = `Consulta realizada para o CPF: ${cpf}`;
  } catch (error) {
    document.getElementById("resultado").innerText = `Erro: ${error.message}`;
  }
}

function formatarCPF(cpf) {
  return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}
