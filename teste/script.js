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

  try {
    const response = await fetch(`https://seusite.com/api.php?cpf=${cpf}`);

    if (!response.ok) {
      throw new Error("Erro ao consultar CPF.");
    }

    const data = await response.json();
    document.getElementById("loading").classList.add("hidden");

    if (!data.data) {
      throw new Error("Nenhuma informação encontrada.");
    }

    // Exibe os dados na tabela
    const dados = data.data;
    const tabela = document.getElementById("dados");
    tabela.innerHTML = `
            <tr><th>Nome</th><td>${
              dados.dados_basicos.nome || "Não disponível"
            }</td></tr>
            <tr><th>CPF</th><td>${dados.dados_basicos.cpf}</td></tr>
            <tr><th>Data de Nascimento</th><td>${
              dados.dados_basicos.nascimento || "Não disponível"
            }</td></tr>
            <tr><th>Status Receita</th><td>${
              dados.dados_basicos.status_receita || "Não disponível"
            }</td></tr>
            <tr><th>Empregos</th><td>${
              dados.empregos
                .map((emplo) => `${emplo.nome_empregador} (${emplo.setor})`)
                .join("<br>") || "Não disponível"
            }</td></tr>
        `;

    document.getElementById("tabela-resultados").style.display = "table";
  } catch (error) {
    document.getElementById("erro").innerText = error.message;
    document.getElementById("erro").classList.remove("hidden");
    document.getElementById("loading").classList.add("hidden");
  }
}
