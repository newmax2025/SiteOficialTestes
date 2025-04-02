document.addEventListener("DOMContentLoaded", function () {
  const userForm = document.getElementById("userForm");
  const removeUserForm = document.getElementById("removeUserForm");
  const statusForm = document.getElementById("statusForm");
  const userListElement = document.getElementById("userList");

  const mensagemCadastro = document.getElementById("mensagemCadastro");
  const mensagemRemocao = document.getElementById("mensagemRemocao");
  const mensagemStatus = document.getElementById("mensagemStatus");

  const statusUserInput = document.getElementById("statusUser");
  const statusSelect = document.getElementById("statusSelect");

  // Verifica se os elementos existem antes de adicionar eventos
  if (!userForm || !removeUserForm || !statusForm || !userListElement) {
    console.error(
      "Erro: Um ou mais elementos do formulário não foram encontrados no HTML."
    );
    return;
  }

  // Função genérica para tratar erros de fetch
  function handleFetchError(error, elementMensagem, tipoAcao) {
    console.error(`Erro ao ${tipoAcao}:`, error);
    elementMensagem.textContent = `Erro ao conectar ao servidor durante ${tipoAcao}. Verifique o console (F12).`;
    elementMensagem.style.color = "red";
  }

  // Atualiza a lista de usuários ao carregar a página
  updateUserList();

  // 📌 Cadastro de novo usuário
  userForm.addEventListener("submit", async function (event) {
    event.preventDefault();
    const username = document.getElementById("newUser").value;
    const password = document.getElementById("newPassword").value;
    mensagemCadastro.textContent = "";

    try {
      const response = await fetch("../backend/cadastro.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password }),
      });

      const result = await response.json();

      if (result.success) {
        mensagemCadastro.textContent =
          result.message || "Usuário cadastrado com sucesso!";
        mensagemCadastro.style.color = "green";
        userForm.reset();
        updateUserList();
      } else {
        mensagemCadastro.textContent = result.message || "Erro ao cadastrar.";
        mensagemCadastro.style.color = "red";
      }
    } catch (error) {
      handleFetchError(error, mensagemCadastro, "cadastro");
    }
  });

  // 📌 Remover usuário
  removeUserForm.addEventListener("submit", async function (event) {
    event.preventDefault();
    const username = document.getElementById("removeUser").value;
    mensagemRemocao.textContent = "";

    try {
      const response = await fetch("../backend/remover.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username }),
      });

      const result = await response.json();

      if (result.success) {
        mensagemRemocao.textContent =
          result.message || "Usuário removido com sucesso!";
        mensagemRemocao.style.color = "green";
        removeUserForm.reset();
        updateUserList();
      } else {
        mensagemRemocao.textContent = result.message || "Erro ao remover.";
        mensagemRemocao.style.color = "red";
      }
    } catch (error) {
      handleFetchError(error, mensagemRemocao, "remoção");
    }
  });

  // 📌 Alterar Status do Usuário
  document
    .getElementById("formAlterarStatus")
    .addEventListener("submit", async function (event) {
      event.preventDefault(); // Evita que o formulário recarregue a página

      let usernameInput = document.getElementById("username");
      let statusInput = document.getElementById("status");

      // Verifica se os elementos existem antes de acessar .value
      if (!usernameInput || !statusInput) {
        console.error("Erro: Campo de usuário ou status não encontrado.");
        return;
      }

      let username = usernameInput.value.trim();
      let status = statusInput.value.trim();

      if (username === "" || status === "") {
        console.error("Erro: Usuário ou status não pode estar vazio.");
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
        console.error("Erro ao enviar requisição:", error);
      }
    });


  // 📌 Atualiza a lista de usuários
  async function updateUserList() {
    userListElement.innerHTML = "<li>Carregando...</li>";

    try {
      const response = await fetch("../backend/listar.php");

      if (!response.ok) {
        let errorText = await response.text();
        throw new Error(
          `Erro do servidor: ${response.status} ${response.statusText}. Resposta: ${errorText}`
        );
      }

      const result = await response.json();

      if (result.success && Array.isArray(result.data)) {
        userListElement.innerHTML = "";
        if (result.data.length === 0) {
          userListElement.innerHTML = "<li>Nenhum usuário cadastrado.</li>";
        } else {
          result.data.forEach((user) => {
            const li = document.createElement("li");
            li.textContent = `${user.usuario} - Status: ${user.status}`;
            userListElement.appendChild(li);
          });
        }
      } else {
        throw new Error(result.message || "Erro ao carregar lista.");
      }
    } catch (error) {
      userListElement.innerHTML =
        "<li style='color: red;'>Erro ao carregar lista.</li>";
      console.error("Erro ao carregar lista:", error);
    }
  }
});
