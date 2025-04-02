document.addEventListener("DOMContentLoaded", function () {
  const userForm = document.getElementById("userForm");
  const removeUserForm = document.getElementById("removeUserForm");
  const statusForm = document.getElementById("statusForm");
  const userListElement = document.getElementById("userList");
  const mensagemCadastro = document.getElementById("mensagemCadastro");
  const mensagemRemocao = document.getElementById("mensagemRemocao");
  const mensagemStatus = document.getElementById("mensagemStatus");

  function handleFetchError(error, elementMensagem, tipoAcao) {
    console.error(`Erro ao ${tipoAcao}:`, error);
    elementMensagem.textContent = `Erro ao conectar ao servidor durante ${tipoAcao}. Verifique o console (F12).`;
    elementMensagem.style.color = "red";
  }

  // Atualiza a lista de usuários ao carregar a página
  updateUserList();

  // Cadastro de novo usuário
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
        mensagemCadastro.textContent = "Usuário cadastrado com sucesso!";
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

  // Remover usuário
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
        mensagemRemocao.textContent = "Usuário removido com sucesso!";
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

  // Alterar status do usuário
  statusForm.addEventListener("submit", async function (event) {
    event.preventDefault();
    const username = document.getElementById("statusUser").value;
    const status = document.getElementById("statusSelect").value;
    mensagemStatus.textContent = "";

    try {
      const response = await fetch("../backend/alterar_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, status }),
      });

      const result = await response.json();

      if (result.success) {
        mensagemStatus.textContent = result.message;
        mensagemStatus.style.color = "green";
        updateUserList();
      } else {
        mensagemStatus.textContent =
          result.message || "Erro ao alterar status.";
        mensagemStatus.style.color = "red";
      }
    } catch (error) {
      handleFetchError(error, mensagemStatus, "alteração de status");
    }
  });

  // Atualiza a lista de usuários
  async function updateUserList() {
    userListElement.innerHTML = "<li>Carregando...</li>";

    try {
      const response = await fetch("../backend/listar.php");

      const result = await response.json();

      if (result.success && Array.isArray(result.data)) {
        userListElement.innerHTML = "";
        if (result.data.length === 0) {
          userListElement.innerHTML = "<li>Nenhum usuário cadastrado.</li>";
        } else {
          result.data.forEach((user) => {
            const li = document.createElement("li");
            li.innerHTML = `${user.usuario} - <strong>${user.status}</strong>`;
            userListElement.appendChild(li);
          });
        }
      } else {
        throw new Error(result.message || "Erro ao listar usuários.");
      }
    } catch (error) {
      userListElement.innerHTML =
        "<li style='color: red;'>Erro ao carregar lista.</li>";
      console.error("Erro ao carregar lista:", error);
    }
  }
});
