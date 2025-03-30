document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("loginForm")
    .addEventListener("submit", async function (event) {
      event.preventDefault();

      const username = document.getElementById("username").value;
      const password = document.getElementById("password").value;
      const errorMessage = document.getElementById("error-message");

      const response = await fetch("login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password }),
      });

      const result = await response.json();

      if (result.success) {
        window.location.href = result.redirect;
      } else {
        errorMessage.textContent = result.message;
        errorMessage.style.color = "red";
      }
    });
});
