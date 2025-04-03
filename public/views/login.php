<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login New Max Consultas</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo md5_file('../assets/css/style.css'); ?>">
</head>
<body>
    <div class="login-container">
        <img src="../assets/img/New Max Buscas.png" alt="Logo do Cliente" class="logo">
        <form id="loginForm">
            <input type="text" id="username" placeholder="Usuário" required>
            <input type="password" id="password" placeholder="Senha" required>

            <!-- Modificação para capturar a resposta do Turnstile -->
            <div id="captcha" class="cf-turnstile" 
            data-sitekey="0x4AAAAAABDPzCDp7OiEAfvh" 
            data-callback="onCaptchaSuccess">
            </div>
            
            <input type="hidden" id="captcha-response" name="cf-turnstile-response">


            <button type="submit">Entrar</button>
        </form>
        <p id="error-message" class="error-message"></p>
    </div>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script>
        function onCaptchaSuccess(token) {
            document.getElementById("captcha-response").value = token;
        }
    </script>

    <script src="../assets/js/login.js?v=<?php echo md5_file('../assets/js/login.js'); ?>"></script>
</body>
</html>
