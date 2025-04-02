document.addEventListener("DOMContentLoaded", function () {
  const userForm = document.getElementById("userForm");
  const removeUserForm = document.getElementById("removeUserForm");
  const statusForm = document.getElementById("statusForm");
  const userListElement = document.getElementById("userList");
  const mensagemCadastro = document.getElementById("mensagemCadastro");
  const mensagemRemocao = document.getElementById("mensagemRemocao");
  const mensagemStatus = document.getElementById("mensagemStatus");

  // Atualiza a lista de usuários ao carregar a página
  updateUserList();

  // Alterar status do usuário
  statusForm.addEventListener("submit", async function (event) {
    event.preventDefault();
    const username = document.getElementById("statusUser").value;
    const status = document.getElementById("newStatus").value;
    mensagemStatus.textContent = ""; // Limpa mensagem anterior

    try {
      const response = await fetch("../backend/alterar_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, status }),
      });

      if (!response.ok) {
        let errorText = await response.text();
        throw new Error(
          `Erro do servidor: ${response.status}. Resposta: ${errorText}`
        );
      }

      const result = await response.json();

      if (result.success) {
        mensagemStatus.textContent =
          result.message || "Status atualizado com sucesso!";
        mensagemStatus.style.color = "green";
        statusForm.reset();
        updateUserList(); // Atualiza a lista de usuários
      } else {
        mensagemStatus.textContent =
          result.message || "Erro ao atualizar status.";
        mensagemStatus.style.color = "red";
      }
    } catch (error) {
      console.error("Erro ao atualizar status:", error);
      mensagemStatus.textContent = "Erro ao conectar ao servidor.";
      mensagemStatus.style.color = "red";
    }
  });

  // Atualiza a lista de usuários
  async function updateUserList() {
    userListElement.innerHTML = "<li>Carregando...</li>";

    try {
      const response = await fetch("../backend/listar.php");

      if (!response.ok) {
        let errorText = await response.text();
        throw new Error(
          `Erro do servidor: ${response.status}. Resposta: ${errorText}`
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
            li.textContent = `${user.usuario} - ${user.status}`;
            userListElement.appendChild(li);
          });
        }
      } else {
        throw new Error(
          result.message || "Resposta inválida do servidor ao listar usuários."
        );
      }
    } catch (error) {
      userListElement.innerHTML =
        "<li style='color: red;'>Erro ao carregar lista.</li>";
      console.error("Erro ao carregar lista:", error);
    }
  }
});
