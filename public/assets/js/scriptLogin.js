document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");

  loginForm.addEventListener("submit", async function (event) {
    event.preventDefault(); // Evita o envio padrão do formulário

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    const errorMessage = document.getElementById("error-message");

    // Validação básica
    if (!username || !password) {
      errorMessage.textContent = "Preencha todos os campos!";
      return;
    }

    try {
      const response = await fetch("login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password }),
      });

      const data = await response.json(); // Converte a resposta para JSON

      if (data.success) {
        window.location.href = data.redirect; // Redireciona para a página correta
      } else {
        errorMessage.textContent = data.message; // Exibe a mensagem de erro
      }
    } catch (error) {
      console.error("Erro na requisição:", error);
      errorMessage.textContent = "Erro ao conectar ao servidor.";
    }
  });
});
