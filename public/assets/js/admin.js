document.addEventListener("DOMContentLoaded", function () {
  const userForm = document.getElementById("userForm");
  const removeUserForm = document.getElementById("removeUserForm");
  const statusForm = document.getElementById("formAlterarStatus");
  const userListElement = document.getElementById("userList");
  const formAlterarPlano = document.getElementById("formAlterarPlano");
  const mensagemPlano = document.getElementById("mensagemPlano");

  const mensagemCadastro = document.getElementById("mensagemCadastro");
  const mensagemRemocao = document.getElementById("mensagemRemocao");
  const mensagemStatus = document.getElementById("mensagemStatus");
  const formAlterarSenha = document.getElementById("formAlterarSenha");
  const mensagemSenha = document.getElementById("mensagemSenha");

  const formMudarVendedor = document.getElementById("formMudarVendedor");
  const mensagemMudarVendedor = document.getElementById(
    "mensagemMudarVendedor"
  );

  // Verifica se os elementos existem antes de adicionar eventos
  if (
    !userForm ||
    !removeUserForm ||
    !statusForm ||
    !userListElement ||
    !formMudarVendedor
  ) {
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

  if (!formMudarVendedor) {
    console.error(
      "Erro: Formulário 'formMudarVendedor' não encontrado no HTML."
    );
    return;
  }

  formMudarVendedor.addEventListener("submit", async function (event) {
    event.preventDefault();

    const clienteInput = document.getElementById("clienteNome");
    const vendedorInput = document.getElementById("novoVendedorId");

    if (!clienteInput || !vendedorInput) {
      console.error(
        "Erro: Campos 'clienteNome' ou 'novoVendedorId' não encontrados no HTML."
      );
      return;
    }

    const cliente = clienteInput.value.trim();
    const vendedor_id = vendedorInput.value.trim();

    if (!cliente || !vendedor_id) {
      alert("Preencha todos os campos!");
      return;
    }

    try {
      const response = await fetch("../backend/admin/mudar_vendedor.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          cliente: cliente,
          vendedor_id: parseInt(vendedor_id),
        }),
      });

      const result = await response.json();

      if (result.success) {
        mensagemMudarVendedor.textContent = "Vendedor atualizado com sucesso!";
        mensagemMudarVendedor.style.color = "green";
      } else {
        mensagemMudarVendedor.textContent = "Erro: " + result.message;
        mensagemMudarVendedor.style.color = "red";
      }
    } catch (error) {
      console.error("Erro na requisição:", error);
      mensagemMudarVendedor.textContent = "Erro ao enviar solicitação.";
      mensagemMudarVendedor.style.color = "red";
    }
  });

  // Cadastro de novo usuário
  userForm.addEventListener("submit", async function (event) {
    event.preventDefault();
    const username = document.getElementById("newUser").value;
    const password = document.getElementById("newPassword").value;
    mensagemCadastro.textContent = "";

    try {
      const response = await fetch("../backend/admin/cadastro.php", {
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

  // Remover usuário
  removeUserForm.addEventListener("submit", async function (event) {
    event.preventDefault();
    const username = document.getElementById("removeUser").value;
    mensagemRemocao.textContent = "";

    try {
      const response = await fetch("../backend/admin/remover.php", {
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

  // Alterar Status do Usuário
  statusForm.addEventListener("submit", async function (event) {
    event.preventDefault();
    const username = document.getElementById("username").value;
    const status = document.getElementById("status").value;
    mensagemStatus.textContent = "";

    try {
      const response = await fetch("../backend/admin/alterar_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, status }),
      });

      const result = await response.json();

      if (result.success) {
        mensagemStatus.textContent =
          result.message || "Status alterado com sucesso!";
        mensagemStatus.style.color = "green";
        statusForm.reset();
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

  // Alterar Senha do Usuário
  formAlterarSenha.addEventListener("submit", async function (event) {
    event.preventDefault();
    const username = document.getElementById("usuarioSenha").value.trim();
    const novaSenha = document.getElementById("novaSenha").value;

    mensagemSenha.textContent = "";

    if (!username || !novaSenha) {
      mensagemSenha.textContent = "Preencha todos os campos.";
      mensagemSenha.style.color = "red";
      return;
    }

    try {
      const response = await fetch("../backend/admin/alterar_senha.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, novaSenha }),
      });

      const result = await response.json();

      if (result.success) {
        mensagemSenha.textContent =
          result.message || "Senha alterada com sucesso!";
        mensagemSenha.style.color = "green";
        formAlterarSenha.reset();
      } else {
        mensagemSenha.textContent = result.message || "Erro ao alterar senha.";
        mensagemSenha.style.color = "red";
      }
    } catch (error) {
      handleFetchError(error, mensagemSenha, "alteração de senha");
    }
  });

  if (formAlterarPlano) {
    formAlterarPlano.addEventListener("submit", async function (event) {
      event.preventDefault();
  
      const username = document.getElementById("usuarioPlano").value.trim();
      const plano = document.getElementById("novoPlano").value;
  
      mensagemPlano.textContent = "";
  
      if (!username || !plano) {
        mensagemPlano.textContent = "Preencha todos os campos.";
        mensagemPlano.style.color = "red";
        return;
      }
  
      try {
        const response = await fetch("../backend/admin/alterar_plano.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ username, plano }),
        });
  
        const result = await response.json();
  
        if (result.success) {
          mensagemPlano.textContent = result.message || "Plano alterado com sucesso!";
          mensagemPlano.style.color = "green";
          formAlterarPlano.reset();
        } else {
          mensagemPlano.textContent = result.message || "Erro ao alterar plano.";
          mensagemPlano.style.color = "red";
        }
      } catch (error) {
        console.error("Erro ao alterar plano:", error);
        mensagemPlano.textContent = "Erro ao enviar solicitação.";
        mensagemPlano.style.color = "red";
      }
    });
  }

  // Atualiza a lista de usuários
  async function updateUserList() {
    userListElement.innerHTML = "<li>Carregando...</li>";

    try {
      const response = await fetch("../backend/admin/listar.php");

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
