<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Membros</title>
    <link rel="stylesheet" href="../assets/css/aM.css?v=<?php echo md5_file('../assets/css/aM.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
        fetch("../backend/get_user_data.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Erro ao carregar os dados do usuário");
            }
            return response.json();
        })
        .then(data => {
            console.log("Dados recebidos:", data); // Para depuração

            if (!data || !data.autenticado) {
                console.warn("Usuário não autenticado, redirecionando...");
                window.location.href = "login.php"; 
                return;
            }

            // Atualiza os elementos da página com os dados do usuário
            document.getElementById("revendedor").innerHTML = `Revendedor: ${data.nome}`;
            document.getElementById("whatsapp").setAttribute("href", data.whatsapp);
            document.getElementById("plano").innerHTML = `Plano: ${data.plano}`;
        })
        .catch(error => {
            console.error("Erro ao carregar os dados do usuário:", error);
            window.location.href = "login.php"; 
        });
        });

    </script>
        
</head>
<body>
    <input type="checkbox" id="check">
    <label for="check">
      <i class="fas fa-bars" id="btn"></i>
      <i class="fas fa-times" id="cancel"></i>
    </label>
    <div class="sidebar">
    <header>Menu</header>
    <ul>
        <li><a href="#"><i class=""></i>Perfil 🔐</a></li>
        <li><a href="#" id="revendedor"><i class=""></i>Revendedor: Carregando...</a></li>
        <li><a href="https://wa.me/" id="whatsapp"><i class="fa-brands fa-whatsapp"></i>Whatsapp</a></li>
        <li><a href="#" id="plano"><i class=""></i>Plano: Carregando...</a></li>
        <a href="../backend/logout.php">Sair</a>
    </ul>
    </div>
    <header>
         <a href="../public/views/aM.php"> <img src="../assets/img/New Max Buscas.png" alt="New Max Buscas.png"></a>
    </header>
     <!-- código do popup -->

 <!-- POPUP -->
<div class="overlay" id="popupOverlay">
    <div class="popup">
      <button class="close-btn" onclick="document.getElementById('popupOverlay').style.display='none'">×</button>
  
      <img src="../assets/img/New Max Buscas.png" alt="Logo NEW MAX BUSCAS" />
  
      <h2>⚠️ Atenção!</h2>
  
      <p>
        Todos os pagamentos devem ser realizados exclusivamente através do link oficial <strong>NewMaxBuscasPay</strong>.
      </p>
      <p>
        ⚠️ <strong>Não efetue pagamentos em nome de pessoas físicas.</strong>
      </p>
      <p>
        O cadastro será liberado automaticamente após a confirmação do pagamento pelo link oficial.
      </p>
  
      <h3>🔒 Aviso Importante!</h3>
  
      <p>
        Nenhum representante está autorizado a enviar login e senha para acesso ao painel de consultas.
      </p>
      <p>
        Nosso sistema é de uso exclusivo para assinantes. Disponibilizamos alguns módulos gratuitos apenas para que você possa experimentar nossos serviços.
      </p>
      <p>
        Para ter acesso completo, é necessário adquirir um plano ativo.
      </p>
      <p>
        Atenciosamente,<br>
        Equipe NEW MAX BUSCAS
      </p>
    </div>
  </div>
    <!-- Banner grande acima da seção de favoritos -->
    <div class="banner-grande" id="banner-grande"></div>

    <section class="carousel" id="favoritos">
        <h2>Favoritos</h2>
        <div class="carousel-container">
            <div class="card grande"> <img src="../assets/img/CRLV DIGITAL Horizontal.jpg" alt="CRLV DIGITAL Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/Impressão CNH Horizontal.jpg" alt="Impressão CNH Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/Consulta SERASA Horizontal.jpg" alt="Consulta SERASA Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/SCPC Horizontal.jpg" alt="SCPC Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/CNH Completa com Foto Horizontal.jpg" alt="CNH Completa com Foto Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/CONSULTA RADAR DE VEÍCULOS Horizontal.jpg" alt="CONSULTA RADAR DE VEÍCULOS Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/CONSULTA Detran Pro Horizontal.jpg" alt="CONSULTA Detran Pro Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/Placa Plus Plano Premium Horinzontal.jpg" alt="Placa Plus Plano Premium Horinzontal"> </div>
            <div class="card grande"> <img src="../assets/img/Consulta  CREDI LINK Horizontal.jpg" alt="Consulta  CREDI LINK Horizontal"> </div>
            <!-- Adicione mais cards conforme necessário -->
        </div>
    </section>
    <section>
        <section class="carousel" id="favoritos">
            <h2> Modulos Gratuitos</h2>
            <div class="carousel-container">
                <div class="card Gratuitos">  
                   <a href="Consulta CPF.html"> <img src="../assets/img/CONSULTA CPF MAX.jpg" alt="CRLV (Todos os Estados)"></a>
                   </div>
                   <div class="card adicionando" onclick="mostrarBotao(this)">  
                    <img src="../assets/img/Consulta Telefone (adicionando).jpg" alt="CRLV (Todos os Estados)">
                    <button class="botao">Sendo Adicionado</button>
                </div>
                  <div class="card adicionando" onclick="mostrarBotao(this)">  
        <img src="../assets/img/Consulta Email (adicionando).jpg" alt="CRLV (Todos os Estados)">
        <button class="botao">Sendo Adicionado</button>
    </div>
    <div class="card adicionando" onclick="mostrarBotao(this)">  
        <img src="../assets/img/Consulta CNPJ (adicionando).jpg" alt="CRLV (Todos os Estados)">
        <button class="botao">Sendo Adicionado</button>
    </div>
    <div class="card adicionando" onclick="mostrarBotao(this)">  
        <img src="../assets/img/Consulta Funcionário (adicionando).jpg" alt="CRLV (Todos os Estados)">
        <button class="botao">Sendo Adicionado</button>
    </div>
    <div class="card adicionando" onclick="mostrarBotao(this)">  
        <img src="../assets/img/Consulta Placa (adicionando).jpg" alt="CRLV (Todos os Estados)">
        <button class="botao">Sendo Adicionado</button>
    </div>
    <div class="card adicionando" onclick="mostrarBotao(this)">  
        <img src="../assets/img/desmascarar Pix (adicionando).jpg" alt="CRLV (Todos os Estados)">
        <button class="botao">Sendo Adicionado</button>
    </div>
    <div class="card adicionando" onclick="mostrarBotao(this)">  
        <img src="../assets/img/desmascarar Pix (adicionando).jpg" alt="CRLV (Todos os Estados)">
        <button class="botao">Sendo Adicionado</button>
    </div>
    </section>
    <section class="carousel" id="favoritos">
        <h2>Exclusivos Premium</h2>
        <div class="carousel-container">
            <div class="card Premium"> 
                
                <img src="../assets/img/Consulta  CREDI LINK.jpg" alt="CRLV (Todos os Estados)">
    
            </div>
            <div class="card Premium"> 
               
                <img src="../assets/img/CNH Completa com Foto.jpg" alt="img/Impressão CNH (Original)a">
                
            </div>
            <div class="card Premium"> 
                
                <img src="../assets/img/Placa Plus Plano Premium.jpg" alt="Consulta SERASA">
                
            </div>
            <div class="card Premium"> 
               
                <img src="../assets/img/cONSULTA Detran Pro.jpg" alt="SCPC">
                
            </div>
    </section>
    <section class="carousel" id="favoritos">

    <h2>Modulos Avançados</h2>
    <div class="carousel-container">
        <div class="card pequeno"> 
            
            <img src="../assets/img/CRLV (Todos os Estados).jpg" alt="CRLV (Todos os Estados)">

        </div>
        <div class="card pequeno"> 
           
            <img src="../assets/img/Impressão CNH (Original).jpg" alt="img/Impressão CNH (Original)a">
            
        </div>
        <div class="card pequeno"> 
            
            <img src="../assets/img/Consulta SERASA.jpg" alt="Consulta SERASA">
            
        </div>
        <div class="card pequeno"> 
           
            <img src="../assets/img/SCPC.jpg" alt="SCPC">
            
        </div>

        <div class="card pequeno"> 
            
            <img src="../assets/img/CONSULTA RADAR DE VEÍCULOS.jpg" alt="CONSULTA RADAR DE VEÍCULOS">
            
        </div>

        <div class="card pequeno"> 
            
            <img src="../assets/img/CNH Simples.jpg" alt="CNH Simples">
            
        </div>
        <div class="card pequeno"> 
            
            <img src="../assets/img/Consulta Veicular Max.jpg" alt="Consulta Veicular Max">
        </div>
        <div class="card pequeno"> 
               
            <img src="../assets/img/Consulta Frota Veicular.jpg" alt="Treino Academia">
          
        </div>
        <div class="card pequeno"> 
            
            <img src="../assets/img/CONSULTA RECEITA FEDERAL.jpg" alt="Treino Academia">
           
        </div>

<div class="card pequeno"> 
   
    <img src="../assets/img/CONSULTA  CADSUS.jpg" alt="CONSULTA  CADSUS">
</div>
<div class="card pequeno"> 
    
    <img src="../assets/img/Consulta Tracker.jpg" alt="Consulta Tracker">
   
</div>
        <!-- Adicione mais cards conforme necessário -->
    </div>
    <!-- Adicione mais Colunas conforme necessário -->
        <section class="carousel" id="treinos">
        <div class="carousel-container">
            <div class="card pequeno"> 
                        
                <img src="../assets/img/Gerar Score.jpg" alt="Treino Academia">
               
            </div>
            <div class="card pequeno"> 
               
                <img src="../assets/img/Buscar Modelo de Veículo.jpg" alt="Treino Academia">
               
            </div>
            <div class="card pequeno"> 
             
                <img src="../assets/img/Gerador de Aniversário.jpg" alt="Treino Academia">
               
            </div>
            <div class="card pequeno"> 
              
                <img src="../assets/img/CONSULTA INSS.jpg" alt="Treino Casa">
                
            </div>

            <div class="card pequeno"> 
                
                <img src="../assets/img/Consulta Tracker AVANÇADO.jpg" alt="Consulta Tracker AVANÇADO">
                
            </div>
            <div class="card pequeno"> 
                
                <img src="../assets/img/Consulta Score.jpg" alt="Consulta Score">
                
            </div>
            <div class="card pequeno"> 
                
                <img src="../assets/img/Consulta DateCorp.jpg" alt="Consulta DateCorp">
                
            </div>
            <div class="card pequeno"> 
               
                <img src="../assets/img/Consulta Search Data.jpg" alt="Consulta Search Data">
              
            </div>
            <div class="card pequeno"> 
               
                <img src="../assets/img/Consulta Dívida.jpg" alt="Consulta Dívida">
               
            </div>
            <div class="card pequeno"> 
               
                <img src="../assets/img/Consulta  Cadin.jpg" alt="Consulta  Cadin">
                
            </div>
            <div class="card pequeno"> 
              
                <img src="../assets/img/CONSULTA EMPRESARIAL.jpg" alt="CONSULTA EMPRESARIAL">
              
            </div>
            
            <!-- Adicione mais cards conforme necessário -->
        </div>
        <section class="carousel" id="treinos">
            <div class="carousel-container">

                <div class="card pequeno"> 
                                     
                    <img src="../assets/img/buscar mandato.jpg" alt="buscar mandato">
                   
                </div>
                <div class="card pequeno"> 
                  
                    <img src="../assets/img/imprimir boletim de ocorrência.jpg" alt="imprimir boletim de ocorrência">
                    
                </div>
                <div class="card pequeno"> 
                    
                    <img src="../assets/img/listagem novos aposentados.jpg" alt="listagem novos aposentados">
                   
                </div>
                <div class="card pequeno"> 
                  
                    <img src="../assets/img/CRV + código.jpg" alt="CRV + código">
                   
                </div>

                    <div class="card pequeno"> 
                        
                        <img src="../assets/img/Buscar Servidor Público.jpg" alt="Treino Academia">
                       
                    </div>
                    <div class="card pequeno"> 
                      
                        <img src="../assets/img/Consultar Empréstimo.jpg" alt="Treino Casa">
                       
                    </div>
                        <div class="card pequeno"> 
                       
                            <img src="../assets/img/óbito.jpg" alt="óbito">
                            </div>
                            
                            <div class="card pequeno"> 
                               
                                <img src="../assets/img/Buscar Foto.jpg" alt="Buscar Foto">
                              
                            </div>
                            <div class="card pequeno"> 
                               
                                <img src="../assets/img/Buscar Processo.jpg" alt="Buscar Processo">
                              
                            </div>
                            <div class="card pequeno"> 
                                
                                <img src="../assets/img/Buscar Assinatura.jpg" alt="Consultar FGTS">
                               
                            </div>
                            <div class="card pequeno"> 
                                
                                <img src="../assets/img/Consultar FGTS.jpg" alt="Buscar Processo">
                               
                            </div>
                        </div>
                        <section class="carousel" id="treinos">
                            <div class="carousel-container">

                                    <div class="card pequeno"> 
                                       
                                        <img src="../assets/img/gerador de rendas.jpg" alt="gerador de rendas">
                                        
                                        </div>
                                        <div class="card pequeno"> 
                                       
                                            <img src="../assets/img/condutor pro.jpg" alt="condutor pro">
                                            </div>
                                            
                                            <div class="card pequeno"> 
                                               
                                                <img src="../assets/img/BACEN.jpg" alt="BACEN">
                                              
                                            </div>

                                            <div class="card pequeno"> 
                                       
                                                <img src="../assets/img/buscar cep.jpg" alt="BACEN">
                                              
                                            </div>

                                            <div class="card pequeno"> 
                                       
                                                <img src="../assets/img/faceMatch.jpg" alt="BACEN">
                                              
                                            </div>

                                            <div class="card pequeno"> 
                                       
                                                <img src="../assets/img/consulta pai e mãe.jpg" alt="BACEN">
                                              
                                            </div>

                                            <div class="card pequeno"> 
                                       
                                                <img src="../assets/img/buscar parentes.jpg" alt="BACEN">
                                              
                                            </div>

                                            <div class="card pequeno"> 
                                               
                                                <img src="../assets/img/Pesquisa por nome.jpg" alt="BACEN">
                                              
                                            </div>

                                            <div class="card pequeno"> 
                                       
                                                <img src="../assets/img/motorista  de 99_uber.jpg" alt="BACEN">
                                              
                                            </div>

                                            <div class="card pequeno"> 
                                       
                                                <img src="../assets/img/motorista  de ifood_uber eats.jpg" alt="BACEN">
                                              
                                            </div>
                                            <!-- Adicione mais cards conforme necessário -->
                                        </div>
                                                </div>
                                                <section class="carousel" id="treinos">
                                                    <section><div id="modal" class="modal">
                                                        <div class="modal-content">
                                                            <p>Contrate o Plano</p>
                                                        </div>
                                                        </section>
                        <footer>
                            <div class="copy">
                                <p>Copyright © 2025 New Max Buscas | All Rights Reserved </p>
                            </div>
                        </footer>
    </section>
    <script>
        
              
  const banner = document.querySelector('.banner-grande');
  const imagens = [
    '../assets/img/assine\ o\ plano\ premium\ .jpg',
    '../assets/img/Banner\ principal\ 1.jpg'
  ];

  let index = 0;

  setInterval(() => {
    index = (index + 1) % imagens.length;
    banner.style.backgroundImage = `url('${imagens[index]}')`;
  }, 4000); // Troca a imagem a cada 4 segundos


        // Função para alterar a imagem do banner grande
        function alterarBanner(imagem) {
            document.getElementById('banner-grande').style.backgroundImage = `url('${imagem}')`;
        }

        document.querySelectorAll('.carousel-container').forEach(container => {
            let isDown = false;
            let startX;
            let scrollLeft;
            container.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
            });
            container.addEventListener('mouseleave', () => {
                isDown = false;
            });
            container.addEventListener('mouseup', () => {
                isDown = false;
            });
            container.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - container.offsetLeft;
                const walk = (x - startX) * 2;
                container.scrollLeft = scrollLeft - walk;
            });

  
         // Função para abrir o modal
         function abrirModal() {
            document.getElementById("modal").style.display = "flex";
        }

        // Função para fechar o modal
        function fecharModal() {
            document.getElementById("modal").style.display = "none";
        }

        // Adiciona o evento de clique aos cards
        document.querySelectorAll('.card.pequeno').forEach(card => {
            card.addEventListener('click', abrirModal);
        });

        // Fecha o modal se o fundo (fora da área modal) for clicado
        document.getElementById("modal").addEventListener('click', function(event) {
            if (event.target === document.getElementById("modal")) {
                fecharModal();
            }
        });
        });
    </script>
</body>
</html>
