document.addEventListener('DOMContentLoaded', () => {
    // --- GERENCIAMENTO DE ESTADO ---
    let allProducts = [];
    let selectedCategory = 'all'; // Categoria selecionada, 'all' é o padrão
    let cartCount = parseInt(localStorage.getItem('cartCount')) || 0;
    let cartTotal = parseFloat(localStorage.getItem('cartTotal')) || 0;

    // --- SELEÇÃO DE ELEMENTOS DO DOM ---
    const cartIcon = document.getElementById('cart-icon');
    const cartCountElement = document.getElementById('cart-count');
    const nameSearch = document.getElementById('name-search');
    const priceFilter = document.getElementById('price-filter');
    const sortOrder = document.getElementById('sort-order');
    const filterBtn = document.getElementById('filter-btn');
    const productList = document.getElementById('product-list');
    const promoTitle = document.getElementById('promo-title');
    const promoDesc = document.getElementById('promo-desc');
    const fakeCountInput = document.getElementById('fake-count');
    const generateFakeBtn = document.getElementById('generate-fake-btn');
    const loadRealBtn = document.getElementById('load-real-btn');
    const categoryFilters = document.querySelectorAll('.category-filter');
    const contactForm = document.getElementById('contact-form');
    const clearCartBtn = document.getElementById('clear-cart-btn');
    const confirmClearBtn = document.getElementById('confirm-clear-btn');

    // --- INICIALIZAÇÃO DE COMPONENTES BOOTSTRAP ---
    const popover = new bootstrap.Popover(cartIcon, {
        html: true,
        trigger: 'hover focus',
        placement: 'bottom',
        title: 'Resumo do Carrinho',
        content: 'Seu carrinho está vazio.' // Conteúdo inicial
    });
    const confirmModal = new bootstrap.Modal(document.getElementById('confirm-clear-cart-modal'));
    const tooltip = new bootstrap.Tooltip(clearCartBtn);

    // --- FUNÇÕES DO CARRINHO ---
    function saveCart() {
        localStorage.setItem('cartCount', cartCount);
        localStorage.setItem('cartTotal', cartTotal);
    }

    function updateCartDisplay() {
        cartCountElement.textContent = cartCount;
        const formattedTotal = cartTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        const popoverContent = cartCount > 0 ? `Total: <strong>${formattedTotal}</strong>` : 'Seu carrinho está vazio.';
        popover.setContent({ '.popover-body': popoverContent });

        // Desabilita o botão de limpar se o carrinho estiver vazio.
        clearCartBtn.disabled = cartCount === 0;
    }

    function clearCart() {
        cartCount = 0;
        cartTotal = 0;
        saveCart();
        updateCartDisplay();
        confirmModal.hide(); // Esconde o modal de confirmação.
    }

    // --- PROMOÇÃO DO DIA (estruturas condicionais) ---
    function getPromotionOfDay(date = new Date()) {
        const weekday = date.getDay(); // 0=Dom, 1=Seg, ... 6=Sáb

        switch (weekday) {
            case 0:
                return { title: 'Domingo de Frete Grátis', desc: 'Frete grátis acima de R$ 199 (cupom: DOM199).' };
            case 1:
                return { title: 'Segunda Tech', desc: 'Até 10% OFF em Eletrônicos (cupom: TECH10).' };
            case 2:
                return { title: 'Terça da Leitura', desc: 'Leve 3 livros e pague 2 (selecionados).' };
            case 3:
                return { title: 'Quarta da Casa', desc: 'Até 15% OFF em Casa & Jardim (cupom: CASA15).' };
            case 4:
                return { title: 'Quinta do Look', desc: 'Roupas com 2ª peça com 30% OFF.' };
            case 5:
                return { title: 'Sextou!', desc: 'Cashback de 5% em todo o site (crédito na loja).' };
            case 6:
                return { title: 'Sábado Relâmpago', desc: 'Oferta surpresa a cada 2 horas. Fique de olho!' };
            default:
                return { title: 'Promoção do Dia', desc: 'Confira as ofertas na vitrine.' };
        }
    }

    function renderPromotion() {
        const promo = getPromotionOfDay();
        if (promoTitle && promoDesc) {
            promoTitle.textContent = promo.title;
            promoDesc.textContent = promo.desc;
        }
    }

    // --- LAB: GERAR PRODUTOS FICTÍCIOS (laços) ---
    function generateFakeProducts(n) {
        const safeN = n > 0 ? n : 1; // operador ternário
        const fakeProducts = [];

        for (let i = 1; i <= safeN; i++) {
            fakeProducts.push({
                imagem: `https://placehold.co/400x400?text=Produto+${i}`,
                nome: `Produto Fictício ${i}`,
                preco: `R$ ${(Math.random() * 900 + 50).toFixed(2)}`.replace('.', ','),
                categoria: i % 2 === 0 ? 'Eletrônicos' : 'Roupas' // ternário + resto
            });
        }

        return fakeProducts;
    }

    // Renderiza os cartões de produtos na página.
    function renderCards(products) {
        productList.innerHTML = '';
        for (const product of products) { // for...of
            const card = `
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <img src="${product.imagem}" class="card-img-top" alt="${product.nome}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${product.nome}</h5>
                            <p class="card-text">${product.preco}</p>
                            <div class="mt-auto">
                                <button class="btn btn-primary">Detalhes</button>
                                <button class="btn btn-success">Comprar</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            productList.innerHTML += card;
        }
        attachProductEventListeners();

        selectCard(); // Permite selecionar os produtos
    }

    // --- SELECIONAR PRODUTO ---
    function selectCard() {
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('click', () => {
                // Limpa anteriores
                cards.forEach(c => c.classList.remove('selected'));

                // Aplica no atual
                card.classList.add('selected');
            });
        });
    }


    function attachProductEventListeners() {
        for (const btn of document.querySelectorAll('#product-list .btn-success')) {
            btn.addEventListener('click', (event) => {
                event.stopPropagation();
                const card = btn.closest('.card');
                const priceString = card.querySelector('.card-text').textContent;
                const price = parseFloat(priceString.replace(/[^0-9,-]+/g, "").replace(',', '.'));

                if (!isNaN(price)) {
                    cartCount++;
                    cartTotal += price;
                    saveCart();
                    updateCartDisplay();
                }
            });
        }
    }

    // --- LÓGICA DE FILTRAGEM E ORDENAÇÃO ---
    function applyFilters() {
        let filteredProducts = [...allProducts];

        if (selectedCategory !== 'all') {
            filteredProducts = filteredProducts.filter(product => product.categoria === selectedCategory);
        }

        const searchTerm = nameSearch.value.toLowerCase();
        if (searchTerm) {
            filteredProducts = filteredProducts.filter(product => product.nome.toLowerCase().includes(searchTerm));
        }

        const maxPrice = parseFloat(priceFilter.value);
        if (!isNaN(maxPrice)) {
            filteredProducts = filteredProducts.filter(product => {
                const price = parseFloat(product.preco.replace(/[^0-9,-]+/g, "").replace(',', '.'));
                return price <= maxPrice;
            });
        }

        const sortValue = sortOrder.value;
        if (sortValue !== 'default') {
            filteredProducts.sort((a, b) => {
                const priceA = parseFloat(a.preco.replace(/[^0-9,-]+/g, "").replace(',', '.'));
                const priceB = parseFloat(b.preco.replace(/[^0-9,-]+/g, "").replace(',', '.'));
                return sortValue === 'asc' ? priceA - priceB : priceB - priceA;
            });
        }

        renderCards(filteredProducts);
    }

    // --- CARGA INICIAL DE DADOS E CONFIGURAÇÃO DE EVENTOS ---
    function loadProductsFromJson() {
        return fetch('produtos.json')
            .then(response => response.json())
            .then(data => {
                allProducts = data;
                applyFilters();
            });
    }

    loadProductsFromJson();

    // Botões do laboratório
    if (generateFakeBtn && fakeCountInput) {
        generateFakeBtn.addEventListener('click', () => {
            const n = parseInt(fakeCountInput.value);
            const fakeProducts = generateFakeProducts(isNaN(n) ? 1 : n);
            allProducts = fakeProducts;
            selectedCategory = 'all';
            applyFilters();
        });
    }

    if (loadRealBtn) {
        loadRealBtn.addEventListener('click', () => {
            loadProductsFromJson();
        });
    }

    // Promoção do dia
    renderPromotion();

    // Eventos de filtro
    nameSearch.addEventListener('input', applyFilters);
    filterBtn.addEventListener('click', applyFilters);
    sortOrder.addEventListener('change', applyFilters);

    categoryFilters.forEach(filter => {
        filter.addEventListener('click', (e) => {
            e.preventDefault();
            selectedCategory = e.target.getAttribute('data-category');
            applyFilters();
        });
    });

    clearCartBtn.addEventListener('click', () => {
        confirmModal.show();
    });

    confirmClearBtn.addEventListener('click', clearCart);

    // --- VALIDAÇÃO DO FORMULÁRIO DE CONTATO ---
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const nameInput = document.getElementById('nome');
            const emailInput = document.getElementById('email');
            const messageTextarea = document.getElementById('mensagem');
            const messageDiv = document.getElementById('form-message');

            messageDiv.innerHTML = '';
            messageDiv.classList.remove('alert', 'alert-danger', 'alert-success');

            if (nameInput.value.trim() === '' || emailInput.value.trim() === '' || messageTextarea.value.trim() === '') {
                messageDiv.textContent = 'Preencha todos os campos!';
                messageDiv.classList.add('alert', 'alert-danger');
            } else {
                messageDiv.textContent = 'Enviado com sucesso!';
                messageDiv.classList.add('alert', 'alert-success');
                contactForm.reset();
            }

            setTimeout(() => {
                messageDiv.innerHTML = '';
                messageDiv.classList.remove('alert', 'alert-danger', 'alert-success');
            }, 3000);
        });
    }

    updateCartDisplay();
});