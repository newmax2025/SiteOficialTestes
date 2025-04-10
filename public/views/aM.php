
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
         <img src="../assets/img/new_max_buscas.png" alt="New Max Buscas.png"></a>
    </header>
     <!-- código do popup -->

 <!-- POPUP -->
<div class="overlay" id="popupOverlay">
    <div class="popup">
      <button class="close-btn" onclick="document.getElementById('popupOverlay').style.display='none'">Fechar</button>
  
      <img src="../assets/img/new_max_buscas.png" alt="Logo NEW MAX BUSCAS" />
  
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
             <div class="card grande"> <img src="../assets/img/CRLV_digital_horizontal.jpg" alt="CRLV DIGITAL Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/Impressão_CNH_horizontal.jpg" alt="Impressão CNH Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/consulta_SERASA_horizontal.jpg" alt="Consulta SERASA Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/SCPC_horizontal.jpg" alt="SCPC Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/CNH_completa_com_foto_horizontal.jpg" alt="CNH Completa com Foto Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/consulta_radar_de_veiculos_horizontal.jpg" alt="CONSULTA RADAR DE VEÍCULOS Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/consulta_detran_pro.jpg" alt="CONSULTA Detran Pro Horizontal"> </div>
            <div class="card grande"> <img src="../assets/img/placa_plus_plano_premium_horinzontal.jpg" alt="Placa Plus Plano Premium Horinzontal"> </div>
            <div class="card grande"> <img src="../assets/img/consulta_credi_link_horizontal.jpg" alt="Consulta  CREDI LINK Horizontal"> </div>
            <!-- Adicione mais cards conforme necessário -->
        </div>
    </section>
    <section>

        <!-- 1° FILEIRA DOS GRATUITOS -->
        <section class="carousel" id="favoritos">
            <h2> Modulos Gratuitos</h2>
            <div class="carousel-container">
             <div class="card Gratuitos">  
                   <a href="consultaCPF.php"> <img src="../assets/img3D/consulta_CPF_max.jpg" alt="consulta_CPF_max"></a>
                   </div>
                   <div class="card Gratuitos" onclick="mostrarBotao(this)">  
                    <a href="consultaTel.php"> <img src="../assets/img3D/buscar_telefone.jpg" alt="consulta_telefone"></a>
                    <button class="botao">Sendo Adicionado</button>
                </div>
                  <div class="card Gratuitos" onclick="mostrarBotao(this)">  
      <a href="consulta_email.php"><img src="../assets/img3D/consulta_EMAIL.jpg" alt="consulta_EMAIL"></a>
        <button class="botao">Sendo Adicionado</button>
    </div>
    <div class="card Gratuitos" onclick="mostrarBotao(this)">  
         <a href="consultaCNPJ.php"> <img src="../assets/img3D/consulta_CNPJ_max.jpg" alt="consulta_CNPJ"></a>
        <button class="botao">Sendo Adicionado</button>
    </div>
    <div class="card Gratuitos" onclick="mostrarBotao(this)">  
        <a href="consulta_placa.php"> <img src="../assets/img3D/consulta_placa_(adicionando).jpg" alt="consulta_placa"></a>
        <button class="botao">Sendo Adicionado</button>
    </div>

                <div class="card Gratuitos" onclick="mostrarBotao(this)">
            <a href="consulta_nome.php"><img src="../assets/img3D/pesquisa_por_nome.jpg" alt="pesquisa_por_nome"></a>
             <button class="botao">Sendo Adicionado</button>
               </div>

    <div class="card Gratuitos" onclick="mostrarBotao(this)">  
       <a href="consulta_cep.php"> <img src="../assets/img3D/buscar_cep.jpg" alt="buscar_cep"></a>
        <button class="botao">Sendo Adicionado</button>
    </div>

    <div class="card Gratuitos" onclick="mostrarBotao(this)">  
        <a href="consulta_pix.php"> <img src="../assets/img3D/desmascarar_pix_(adicionando).jpg" alt="desmascarar_pix_(adicionando)"></a>
         <button class="botao">Sendo Adicionado</button>
           </div>
        </div>

        <!-- 2° FILEIRA DOS GRATUITOS -->
           <section class="carousel" id="treinos">
            <div class="carousel-container">

               <div class="card Gratuitos" onclick="mostrarBotao(this)">
                <div class="tarja">Manutenção</div> <!-- Tarja adicionada aqui --> 
                <img src="../assets/img3D/buscar_chassi.jpg" alt="pesquisa_por_nome">
                 <button class="botao">Sendo Adicionado</button>
                   </div>

                   <div class="card Gratuitos" onclick="mostrarBotao(this)">
                    <div class="tarja">Manutenção</div> <!-- Tarja adicionada aqui --> 
                    <img src="../assets/img3D/Foto_rj.jpg" alt="pesquisa_por_nome">
                     <button class="botao">Sendo Adicionado</button>
                       </div>

                       <div class="card Gratuitos" onclick="mostrarBotao(this)">
                        <div class="tarja">Manutenção</div> <!-- Tarja adicionada aqui -->
                        <img src="../assets/img3D/Foto_sp.jpg" alt="pesquisa_por_nome">
                         <button class="botao">Sendo Adicionado</button>
                           </div>
    </div>
    </section>
    <section class="carousel2" id="favoritos">
          <h2>
            Exclusivos Premium 
            <span class="estrelas-animadas">
              <span>★</span>
              <span>★</span>
              <span>★</span>
              <span>★</span>
              <span>★</span>
            </span>
          </h2>
        <div class="carousel-container">
            <div class="card Premium"> 
                
                <img src="../assets/img3D/consulta_credi_link.jpg" alt="consulta_credi_link">
    
            </div>
            <div class="card Premium"> 

                 <img src="../assets/img3D/CNH_completa_com_foto.jpg" alt="CNH_completa_com_foto">
                
            </div>
            <div class="card Premium"> 
                
                <img src="../assets/img3D/placaplus_premium.jpg" alt="placa_plus_plano_premium">
                
            </div>
            <div class="card Premium"> 
               
                <img src="../assets/img3D/consulta_detran_pro.jpg" alt="consulta_detran_pro.jpg">
                
            </div>
            <div class="card Premium"> 
               
                <img src="../assets/img3D/despachante_condutor.jpg" alt="despachante_condutor">
                
            </div>
            <div class="card Premium"> 
               
                <img src="../assets/img3D/despachante_veicular.jpg" alt="despachante_veicular">
                
            </div> <div class="card Premium"> 
               
                <img src="../assets/img3D/consulta_radar_de_veiculos.jpg" alt="consulta_radar_de_veiculos">

            </div>
            <!-- Adicione mais cards conforme necessário -->
        </div>

            
    </section>
    <section class="carousel" id="favoritos">


        <!-- 1° FILEIRA DOS ASSINANTES -->
    <h2>Modulos Assinantes</h2>
    <div class="carousel-container">
        <div class="card pequeno"> 
            
            <img src="../assets/img3D/CRLV_(Todos_os_estados).jpg" alt="CRLV_(Todos_os_estados)">

        </div>

        <div class="card pequeno"> 
            
            <img src="../assets/img/consulta_SERASA.jpg" alt="consulta_SERASA.jpg">
            
        </div>

        <div class="card pequeno"> 
           
            <img src="../assets/img/scpc.jpg" alt="SCPC">
            
        </div>

        <div class="card pequeno"> 
            
            <img src="../assets/img/CNH_simples.jpg" alt="CNH_simples">
            
        </div>
        <div class="card pequeno"> 
            
            <img src="../assets/img/consulta_veicular_max.jpg" alt="consulta_veicular_max">
        </div>
        <div class="card pequeno"> 
               
            <img src="../assets/img/consulta_frota_veicular.jpg" alt="consulta_frota_veicular">
          
        </div>
        <div class="card pequeno"> 
            
            <img src="../assets/img3D/consulta_receita_federal.jpg" alt="consulta_receita_federal">
           
                            </div>

                            <div class="card pequeno"> 
   
                            <img src="../assets/img/consulta_cadsus.jpg" alt="consulta_cadsus">
                          </div>
                            <div class="card pequeno"> 
    
                              <img src="../assets/img/consulta_tracker.jpg" alt="consulta_tracker">
   
                               </div>
                            <div class="card pequeno"> 
                                
                                <img src="../assets/img/consultar_FGTS.jpg" alt="consultar_FGTS">
                               
                            </div>
                            <div class="card pequeno"> 
                                               
                                <img src="../assets/img3D/bacen.jpg" alt="bacen">
                              
                            </div>
        <!-- Adicione mais cards conforme necessário -->
    </div>
    <!-- Adicione mais Colunas conforme necessário -->

    <!-- 2° FILEIRA DOS ASSINANTES -->
        <section class="carousel" id="treinos">
        <div class="carousel-container">
            <div class="card pequeno"> 
                        
                <img src="../assets/img/gerar_score.jpg" alt="gerar_score">
               
            </div>

            <div class="card pequeno"> 
             
                <img src="../assets/img/gerador_de_aniversario.jpg" alt="gerador_de_aniversario">
               
            </div>
            <div class="card pequeno"> 
              
                <img src="../assets/img/consulta_INSS.jpg" alt="consulta_INSS">
                
            </div>

            <div class="card Gratuitos" onclick="mostrarBotao(this)">  
                <img src="../assets/img/consulta_funcionarios.jpg" alt="consulta_funcionarios">
                <button class="botao">Sendo Adicionado</button>
            </div>

            <div class="card pequeno"> 
                
                <img src="../assets/img/consulta_tracker_avançado.jpg" alt="consulta_tracker_avançado">
                
            </div>
            <div class="card pequeno"> 
                
                <img src="../assets/img/consulta_score.jpg" alt="consulta_score">
                
            </div>
            <div class="card pequeno"> 
                
                <img src="../assets/img/consulta_datecorp.jpg" alt="consulta_datecorp">
                
            </div>
            <div class="card pequeno"> 
               
                <img src="../assets/img/consulta_search_data.jpg" alt="consulta_search_dataa">
              
            </div>
            <div class="card pequeno"> 
               
                <img src="../assets/img/consulta_divida.jpg" alt="consulta_divida">
               
            </div>
            <div class="card pequeno"> 
               
                <img src="../assets/img/consulta_cadin.jpg" alt="consulta_cadin">
                
            </div>
            <div class="card pequeno"> 
              
                <img src="../assets/img/consulta_empresarial.jpg" alt="consulta_empresarial">
              
            </div>
            
            <!-- Adicione mais cards conforme necessário -->
        </div>


         <!-- 3° FILEIRA DOS ASSINANTES -->
        <section class="carousel" id="treinos">
            <div class="carousel-container">

                <div class="card pequeno"> 
                                     
                    <img src="../assets/img/buscar_mandato.jpg" alt="buscar_mandato">
                   
                </div>
                <div class="card pequeno"> 
                  
                    <img src="../assets/img/imprimir_boletim_de_ocorrência.jpg" alt="imprimir_boletim_de_ocorrência">
                    
                </div>
                <div class="card pequeno"> 
                    
                    <img src="../assets/img/listagem_novos_aposentados.jpg" alt="listagem_novos_aposentados">
                   
                </div>
                <div class="card pequeno"> 
                  
                    <img src="../assets/img/CRV_+código.jpg" alt="CRV_+código">
                   
                </div>

                    <div class="card pequeno"> 
                        
                        <img src="../assets/img/buscar_servidor_publico.jpg" alt="buscar_servidor_publico">
                       
                    </div>
                    <div class="card pequeno"> 
                      
                        <img src="../assets/img/consultar_empréstimo.jpg" alt="consultar_empréstimo">
                       
                    </div>
                        <div class="card pequeno"> 
                       
                            <img src="../assets/img/obito.jpg" alt="obito">
                            </div>
                            
                            <div class="card pequeno"> 
                               
                                <img src="../assets/img/buscar_foto.jpg" alt="buscar_foto">
                              
                            </div>
                            <div class="card pequeno"> 
                               
                                <img src="../assets/img/buscar_processo.jpg" alt="buscar_processo">
                              
                            </div>
                            <div class="card pequeno"> 
                                
                                <img src="../assets/img/buscar_assinatura.jpg" alt="buscar_assinatura">
                               
                            </div>
                 <div class="card pequeno"> 
                                       
                                        <img src="../assets/img/gerador_de_rendas.jpg" alt="gerador_de_rendas">
                                        
                                        </div>
                                    </div>


                        <!-- 4° FILEIRA DOS ASSINANTES -->
                        <section class="carousel" id="treinos">
                            <div class="carousel-container">

                                            <div class="card pequeno"> 
                                       
                                                <img src="../assets/img/consulta_pai_e_mãe.jpg" alt="consulta_pai_e_mãe">
                                              
                                            </div>

                                            <div class="card pequeno"> 
                                       
                                                <img src="../assets/img/buscar_parentes.jpg" alt="buscar_parentes">
                                              
                                            </div>

                                            <div class="card pequeno"> 
                                       
                                                <img src="../assets/img/motorista_de_99_uber.jpg" alt="/motorista_de_99_uber">
                                              
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
    '../assets/img/banner_new_MAX.png',
    '../assets/img/assine_o_plano_premium2.png'
    
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
