document.addEventListener("DOMContentLoaded", function () {
  const userForm = document.getElementById("userForm");
  const removeUserForm = document.getElementById("removeUserForm");
  const userListElement = document.getElementById("userList");
  const mensagemCadastro = document.getElementById("mensagemCadastro");
  const mensagemRemocao = document.getElementById("mensagemRemocao");
  // Adiciona um elemento para erros gerais ou de listagem, se necessário
  const mensagemGeral = document.getElementById("mensagemGeral") || {
    textContent: "",
    style: {},
  }; // Cria um objeto dummy se não existir

  // Função genérica para tratar erros de fetch
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
    mensagemCadastro.textContent = ""; // Limpa mensagem anterior

    try {
      // --- CORRIGIDO: Caminho para o backend ---
      const response = await fetch("../backend/cadastro.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        // O PHP ignora 'tipoUsuario', mas podemos manter ou remover do JS
        body: JSON.stringify({
          username,
          password /*, tipoUsuario: "cliente" */,
        }),
      });

      // Verifica se a resposta foi OK (status 2xx) antes de tentar ler JSON
      if (!response.ok) {
        // Tenta ler a resposta como texto para ver se há erro do servidor
        let errorText = await response.text();
        throw new Error(
          `Erro do servidor: ${response.status} ${response.statusText}. Resposta: ${errorText}`
        );
      }

      const result = await response.json();

      if (result.success) {
        mensagemCadastro.textContent =
          result.message || "Usuário cadastrado com sucesso!"; // Usa a msg do backend ou uma padrão
        mensagemCadastro.style.color = "green";
        userForm.reset(); // Limpa o formulário
        updateUserList(); // Atualiza a lista
      } else {
        mensagemCadastro.textContent =
          result.message || "Ocorreu um erro ao cadastrar."; // Mensagem de erro do backend
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
    mensagemRemocao.textContent = ""; // Limpa mensagem anterior

    try {
      // --- CORRIGIDO: Caminho para o backend ---
      const response = await fetch("../backend/remover.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username }),
      });

      if (!response.ok) {
        let errorText = await response.text();
        throw new Error(
          `Erro do servidor: ${response.status} ${response.statusText}. Resposta: ${errorText}`
        );
      }

      const result = await response.json();

      if (result.success) {
        mensagemRemocao.textContent =
          result.message || "Usuário removido com sucesso!";
        mensagemRemocao.style.color = "green";
        removeUserForm.reset(); // Limpa o formulário
        updateUserList(); // Atualiza a lista
      } else {
        mensagemRemocao.textContent =
          result.message || "Ocorreu um erro ao remover.";
        mensagemRemocao.style.color = "red";
      }
    } catch (error) {
      handleFetchError(error, mensagemRemocao, "remoção");
    }
  });

  // Atualiza a lista de usuários
  async function updateUserList() {
    mensagemGeral.textContent = ""; // Limpa mensagens gerais
    userListElement.innerHTML = "<li>Carregando...</li>"; // Feedback visual

    try {
      // --- CORRIGIDO: Caminho para o backend ---
      const response = await fetch("../backend/listar.php");

      if (!response.ok) {
        let errorText = await response.text();
        throw new Error(
          `Erro do servidor: ${response.status} ${response.statusText}. Resposta: ${errorText}`
        );
      }

      const result = await response.json();

      // --- CORRIGIDO: Verifica 'success' e acessa 'data' ---
      if (result.success && Array.isArray(result.data)) {
        userListElement.innerHTML = ""; // Limpa o 'Carregando...'
        if (result.data.length === 0) {
          userListElement.innerHTML = "<li>Nenhum usuário cadastrado.</li>";
        } else {
          result.data.forEach((user) => {
            const li = document.createElement("li");
            li.textContent = user.usuario; // Assume que o objeto user tem a propriedade 'usuario'
            userListElement.appendChild(li);
          });
        }
      } else {
        // Se success for false ou data não for array
        throw new Error(
          result.message || "Resposta inválida do servidor ao listar usuários."
        );
      }
    } catch (error) {
      userListElement.innerHTML = ""; // Limpa o 'Carregando...'
      handleFetchError(error, mensagemGeral, "listagem de usuários"); // Mostra erro na área geral
      // Alternativamente, pode mostrar no próprio userListElement:
      // userListElement.innerHTML = `<li style="color: red;">Erro ao carregar lista.</li>`;
      // console.error("Erro ao carregar lista:", error);
    }
  }
});
