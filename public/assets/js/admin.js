document.addEventListener("DOMContentLoaded", function () {
  // ====================== CADASTRAR USUÁRIO ======================
  let formCadastro = document.getElementById("formCadastro");
  if (formCadastro) {
    formCadastro.addEventListener("submit", async function (event) {
      event.preventDefault(); // Evita recarregar a página

      let username = document.getElementById("cadastroUsername").value.trim();
      let password = document.getElementById("cadastroPassword").value.trim();

      if (username === "" || password === "") {
        alert("Preencha todos os campos!");
        return;
      }

      try {
        let response = await fetch("cadastrar.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ username: username, password: password }),
        });

        let result = await response.json();
        console.log(result);

        if (result.success) {
          alert("Usuário cadastrado com sucesso!");
          formCadastro.reset();
        } else {
          alert("Erro ao cadastrar: " + result.message);
        }
      } catch (error) {
        console.error("Erro ao cadastrar usuário:", error);
      }
    });
  } else {
    console.error("Erro: Formulário de cadastro não encontrado.");
  }

  // ====================== ALTERAR STATUS DO USUÁRIO ======================
  let formAlterarStatus = document.getElementById("formAlterarStatus");
  if (formAlterarStatus) {
    formAlterarStatus.addEventListener("submit", async function (event) {
      event.preventDefault();

      let usernameInput = document.getElementById("username");
      let statusInput = document.getElementById("status");

      if (!usernameInput || !statusInput) {
        console.error("Erro: Campo de usuário ou status não encontrado.");
        return;
      }

      let username = usernameInput.value.trim();
      let status = statusInput.value.trim();

      if (username === "" || status === "") {
        alert("Preencha todos os campos!");
        return;
      }

      try {
        let response = await fetch("alterar_status.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ username: username, status: status }),
        });

        let result = await response.json();
        console.log(result);

        if (result.success) {
          alert("Status atualizado com sucesso!");
        } else {
          alert("Erro ao atualizar status: " + result.message);
        }
      } catch (error) {
        console.error("Erro ao alterar status:", error);
      }
    });
  } else {
    console.error("Erro: Formulário de alteração de status não encontrado.");
  }

  // ====================== LISTAR USUÁRIOS ======================
  async function listarUsuarios() {
    try {
      let response = await fetch("listar_usuarios.php");
      let result = await response.json();

      let tabelaUsuarios = document.getElementById("tabelaUsuarios");
      if (!tabelaUsuarios) {
        console.error("Erro: Tabela de usuários não encontrada.");
        return;
      }

      tabelaUsuarios.innerHTML = "";

      result.forEach((user) => {
        let row = document.createElement("tr");
        row.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.username}</td>
                    <td>${user.status}</td>
                `;
        tabelaUsuarios.appendChild(row);
      });
    } catch (error) {
      console.error("Erro ao listar usuários:", error);
    }
  }

  // Chama a função ao carregar a página
  listarUsuarios();
});
