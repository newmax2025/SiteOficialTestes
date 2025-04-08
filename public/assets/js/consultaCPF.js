let captchaValidado = false;

const interessesLabels = {
    credit_personal_pre_approved: "Crédito Pessoal Pré-Aprovado",
    credit_real_estate_pre_approved: "Crédito Imobiliário Pré-Aprovado",
    vehicle_financing_pre_approved: "Financiamento de Veículos Pré-Aprovado",
    middle_class: "Classe Média",
    automatic_debit: "Débito Automático",
    has_luxury: "Possui Itens de Luxo",
    has_investments: "Possui Investimentos",
    has_credit_card: "Possui Cartão de Crédito",
    has_multiple_cards: "Possui Múltiplos Cartões",
    has_high_standard_account: "Conta de Alto Padrão",
    has_black_card: "Possui Cartão Black",
    has_prime_card: "Possui Cartão Prime",
    has_prepaid_cell: "Celular Pré-Pago",
    has_postpaid_cell: "Celular Pós-Pago",
    has_accumulated_miles: "Possui Milhas Acumuladas",
    has_own_house: "Possui Casa Própria",
    has_discounts: "Utiliza Descontos",
    has_checking_accounts: "Possui Conta Corrente",
    has_auto_insurance: "Possui Seguro Automotivo",
    has_private_pension: "Possui Previdência Privada",
    has_internet_banking: "Utiliza Internet Banking",
    has_token_installed: "Token de Segurança Instalado",
    has_traveled: "Já Viajou",

    // Probabilidades
    personal_credit_probability: "Probabilidade de Crédito Pessoal",
    vehicle_financing_probability: "Probabilidade de Financiamento de Veículos",
    internet_shopping_probability: "Probabilidade de Compras Online",
    multiple_cards_probability: "Probabilidade de Múltiplos Cartões",
    prime_card_probability: "Probabilidade de Cartão Prime",
    cable_tv_probability: "Probabilidade de TV por Assinatura",
    broadband_probability: "Probabilidade de Banda Larga",
    own_house_probability: "Probabilidade de Ter Casa Própria",
    prepaid_cell_probability: "Probabilidade de Celular Pré-Pago",
    postpaid_cell_probability: "Probabilidade de Celular Pós-Pago",
    real_estate_credit_probability: "Probabilidade de Crédito Imobiliário",
    auto_insurance_probability: "Probabilidade de Seguro Automotivo",
    health_insurance_probability: "Probabilidade de Plano de Saúde",
    life_insurance_probability: "Probabilidade de Seguro de Vida",
    home_insurance_probability: "Probabilidade de Seguro Residencial",
    investments_probability: "Probabilidade de Ter Investimentos",
    consigned_probability: "Probabilidade de Empréstimo Consignado",
    private_pension_probability: "Probabilidade de Previdência Privada",
    miles_redemption_probability: "Probabilidade de Resgate de Milhas",
    discount_hunter_probability: "Probabilidade de Ser Caçador de Descontos",
    fitness_probability: "Probabilidade de Estilo de Vida Fitness",
    tourism_probability: "Probabilidade de Interesse em Turismo",
    luxury_probability: "Probabilidade de Interesse em Luxo",
    cinephile_probability: "Probabilidade de Ser Cinéfilo",
    public_transport_probability: "Probabilidade de Uso de Transporte Público",
    online_games_probability: "Probabilidade de Interesse em Jogos Online",
    video_game_probability: "Probabilidade de Interesse em Video Games",
    early_adopters_probability: "Probabilidade de Ser um Inovador (Early Adopter)"
};

function gerarPDF() {
  const dadosElement = document.getElementById("dados");
  const resultadoElement = document.getElementById("resultado");
  const doc = new jsPDF();
  doc.setFontSize(12);

  const content = dadosElement.innerText || "Sem dados para exportar.";
  const title = resultadoElement.innerText;

  doc.text(title, 10, 10);

  const splitContent = doc.splitTextToSize(content, 180);
  doc.text(splitContent, 10, 20);

  doc.save("relatorio-cpf.pdf");
}

// Após consulta bem-sucedida, chame gerarPDF() quando o botão de download for clicado
// Exemplo de botão:
// <button onclick="gerarPDF()">Baixar PDF</button>
