<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Painel de Administração</title>
    <link rel="stylesheet" href="../assets/css/admin.css?v=<?php echo md5_file('../assets/css/admin.css'); ?>">
    <script>
      fetch("../backend/verifica_sessao.php")
        .then((response) => response.json())
        .then((data) => {
          if (!data.autenticado) {
            window.location.href = "login.html"; // Redireciona se não estiver autenticado
          }
        })
        .catch((error) => {
          console.error("Erro ao verificar sessão:", error);
          window.location.href = "login.html"; // Opcional: Redireciona em caso de erro
        });
    </script>
  </head>

  <body>
    <div class="admin-container">
      <h2>Painel de Administração</h2>

      <h3>Adicionar Novo Usuário</h3>
      <form id="userForm">
        <input
          type="text"
          id="newUser"
          placeholder="Novo Usuário (Email)"
          required
        />
        <input
          type="password"
          id="newPassword"
          placeholder="Nova Senha"
          required
        />
        <button type="submit">Adicionar Usuário</button>
      </form>
      <p id="mensagemCadastro"></p>

      <h3>Usuários Cadastrados</h3>
      <ul id="userList"></ul>

      <h3>Remover Usuário</h3>
      <form id="removeUserForm">
        <input
          type="text"
          id="removeUser"
          placeholder="Usuário para Remover"
          required
        />
        <button type="submit">Remover Usuário</button>
      </form>
      <p id="mensagemRemocao"></p>

      <h3>Alterar Status do Usuário</h3>
      <form id="formAlterarStatus">
        <label for="username">Nome de usuário:</label>
        <input type="text" id="username" required />

        <label for="status">Status:</label>
        <select id="status" required>
          <option value="ativo">Ativo</option>
          <option value="inativo">Inativo</option>
        </select>

        <button type="submit">Alterar Status</button>
      </form>
      <p id="mensagemStatus"></p>
      
      <h3>Mudar Vendedor do Cliente</h3>
      <form id="formMudarVendedor">
        <label for="clienteNome">Nome do Cliente:</label>
        <input type="text" id="clienteNome" required />

        <label for="novoVendedorId">Novo Vendedor (ID):</label>
        <input type="number" id="novoVendedorId" required />

        <button type="submit">Mudar Vendedor</button>
      </form>
      <p id="mensagemMudarVendedor"></p>

      <button onclick="window.location.href='index.html'">Sair</button>
    </div>

    <script src="../assets/js/admin.js?v=<?php echo md5_file('../assets/js/admin.js'); ?>"></script>
  </body>
</html>
