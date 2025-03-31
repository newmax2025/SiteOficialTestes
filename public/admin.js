document.addEventListener("DOMContentLoaded", function () {
  const userForm = document.getElementById("userForm");
  const removeUserForm = document.getElementById("removeUserForm");
  const userListElement = document.getElementById("userList");

  // Atualiza a lista de usuários ao carregar a página
  updateUserList();

  // Cadastro de novo usuário
  userForm.addEventListener("submit", async function (event) {
    event.preventDefault();
    const username = document.getElementById("newUser").value;
    const password = document.getElementById("newPassword").value;
    const mensagemCadastro = document.getElementById("mensagemCadastro");

    const response = await fetch("cadastro.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username, password, tipoUsuario: "cliente" }),
    });

    const result = await response.json();

    if (result.success) {
      mensagemCadastro.textContent = "Usuário cadastrado com sucesso!";
      mensagemCadastro.style.color = "green";
      updateUserList();
    } else {
      mensagemCadastro.textContent = result.message;
      mensagemCadastro.style.color = "red";
    }
  });

  // Remover usuário
  removeUserForm.addEventListener("submit", async function (event) {
    event.preventDefault();
    const username = document.getElementById("removeUser").value;
    const mensagemRemocao = document.getElementById("mensagemRemocao");

    const response = await fetch("remover.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username }),
    });

    const result = await response.json();

    if (result.success) {
      mensagemRemocao.textContent = "Usuário removido com sucesso!";
      mensagemRemocao.style.color = "green";
      updateUserList();
    } else {
      mensagemRemocao.textContent = result.message;
      mensagemRemocao.style.color = "red";
    }
  });

  // Atualiza a lista de usuários
  async function updateUserList() {
    const response = await fetch("listar.php");
    const users = await response.json();
    userListElement.innerHTML = "";

    users.forEach((user) => {
      const li = document.createElement("li");
      li.textContent = user.usuario;
      userListElement.appendChild(li);
    });
  }
});
