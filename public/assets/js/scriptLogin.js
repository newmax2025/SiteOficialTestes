document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const errorMessage = document.getElementById("error-message"); // Definido fora para limpar antes

  loginForm.addEventListener("submit", async function (event) {
    event.preventDefault(); // Evita o envio padrão do formulário

    const usernameInput = document.getElementById("username");
    const passwordInput = document.getElementById("password");
    // const captchaResponse = document.getElementById("captcha-response").value; // Se for usar o captcha

    const username = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    errorMessage.textContent = ""; // Limpa mensagens anteriores

    // Validação básica
    if (!username || !password) {
      errorMessage.textContent = "Preencha o usuário e a senha!";
      return;
    }

    try {
      // VERIFIQUE ESTE CAMINHO:
      // É relativo ao ARQUIVO HTML (provavelmente views/login.html) que inclui este script.
      // Se login.html está em views/, e login.php está em backend/, '../backend/login.php' está CORRETO.
      const response = await fetch("../backend/login.php", {
        // <-- VERIFICAR CAMINHO
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json", // Boa prática adicionar Accept
        },
        // body: JSON.stringify({ username, password, captchaResponse }), // Se for usar o captcha
        body: JSON.stringify({ username, password }),
      });

      // Verifica se a resposta da rede foi OK (status 2xx)
      if (!response.ok) {
        // Tenta ler uma mensagem de erro JSON do backend, se houver
        let errorData = {
          message: `Erro HTTP: ${response.status} ${response.statusText}`,
        }; // Mensagem padrão
        try {
          errorData = await response.json();
        } catch (jsonError) {
          // Se a resposta não for JSON, usa a mensagem padrão
          console.error(
            "A resposta do servidor não foi JSON:",
            await response.text()
          );
        }
        throw new Error(errorData.message || `Erro ${response.status}`); // Lança um erro para o catch
      }

      // Se a resposta de rede foi OK, processa o JSON
      const data = await response.json();

      if (data.success && data.redirect) {
        // Sucesso - Redireciona para a página indicada pelo backend
        // IMPORTANTE: Certifique-se que os caminhos como 'aM.html' ou 'admin.html'
        // são acessíveis a partir da raiz do seu site ou ajuste o redirecionamento.
        // Se eles estão em /views/, o redirecionamento pode precisar ser '../views/aM.html'
        // ou configurar o servidor web para encontrar essas páginas.
        window.location.href = data.redirect;
      } else {
        // Falha informada pelo backend (ex: senha errada)
        errorMessage.textContent = data.message || "Ocorreu um erro no login.";
      }
    } catch (error) {
      // Erro de rede, falha no fetch, erro lançado pelo !response.ok, ou erro ao processar JSON
      console.error("Erro na requisição de login:", error);
      // Exibe a mensagem de erro capturada ou uma mensagem padrão
      errorMessage.textContent =
        error.message ||
        "Erro ao conectar ao servidor ou processar a resposta.";
      // Limpa campos se desejar, após erro grave
      // usernameInput.value = "";
      // passwordInput.value = "";
    }
  });

  // Função para lidar com o sucesso do CAPTCHA (se for usar)
  // window.onCaptchaSuccess = function(token) {
  //     const captchaInput = document.getElementById('captcha-response');
  //     if (captchaInput) {
  //         captchaInput.value = token;
  //     } else {
  //         console.error("Elemento #captcha-response não encontrado.");
  //     }
  // };
});

// Necessário se você descomentar o CAPTCHA no HTML
// <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
