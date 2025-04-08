<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Buscador de Sites</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f4f4f9;
      padding: 40px;
      max-width: 800px;
      margin: auto;
    }

    h1 {
      color: #333;
      text-align: center;
    }

    input[type="text"] {
      width: 80%;
      padding: 10px;
      font-size: 16px;
      margin-right: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    button {
      padding: 10px 15px;
      font-size: 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    button:hover {
      opacity: 0.9;
    }

    .search-btn {
      background-color: #4CAF50;
      color: white;
    }

    .pdf-btn {
      background-color: #2196F3;
      color: white;
      margin-top: 20px;
    }

    #results {
      margin-top: 30px;
    }

    .result-item {
      background-color: white;
      border-radius: 6px;
      padding: 15px;
      margin-bottom: 15px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .result-item a {
      color: #2196F3;
      font-weight: bold;
      text-decoration: none;
    }

    .result-item a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <h1>Buscador de Sites</h1>
  <input type="text" id="searchInput" placeholder="Digite sua busca..." />
  <button class="search-btn" onclick="search()">Buscar</button>

  <div id="results"></div>
  <button class="pdf-btn" onclick="generatePDF()" style="display: none;" id="savePdfBtn">Salvar em PDF</button>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

  <script>
    async function search() {
      const query = document.getElementById("searchInput").value;
      const response = await fetch(`https://api.duckduckgo.com/?q=${encodeURIComponent(query)}&format=json&no_redirect=1&no_html=1&skip_disambig=1`);
      const data = await response.json();

      const resultsContainer = document.getElementById("results");
      resultsContainer.innerHTML = '';

      const saveBtn = document.getElementById("savePdfBtn");

      if (data.RelatedTopics.length === 0) {
        resultsContainer.innerHTML = "<p>Nenhum resultado encontrado.</p>";
        saveBtn.style.display = "none";
        return;
      }

      data.RelatedTopics.forEach(topic => {
        if (topic.Text && topic.FirstURL) {
          const item = document.createElement("div");
          item.className = "result-item";
          item.innerHTML = `<p>${topic.Text}</p><a href="${topic.FirstURL}" target="_blank">${topic.FirstURL}</a>`;
          resultsContainer.appendChild(item);
        }
      });

      saveBtn.style.display = "inline-block";
    }

    function generatePDF() {
      const results = document.getElementById("results");
      const clone = results.cloneNode(true);

      const container = document.createElement("div");
      container.style.padding = "20px";
      container.style.fontFamily = "Arial";

      const logo = document.createElement("img");
      logo.src = "https://via.placeholder.com/200x50?text=Seu+Logo"; // Substitua pelo seu logo real
      logo.style.display = "block";
      logo.style.margin = "0 auto 20px";
      logo.style.maxWidth = "200px";

      const title = document.createElement("h2");
      title.innerText = "Resultados da Busca";
      title.style.textAlign = "center";
      title.style.color = "#333";
      title.style.marginBottom = "20px";

      container.appendChild(logo);
      container.appendChild(title);
      container.appendChild(clone);

      html2pdf().from(container).set({
        margin: 10,
        filename: 'resultados-da-busca.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      }).save();
    }
  </script>
</body>
</html>
