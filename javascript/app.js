function saudacao() {
  alert('Olá, Mundo! - arquivo');
}

function prepararBotaoDetalhes() {
    /*
    document.querySelectorAll('.product-card button').forEach(btn => {
        btn.addEventListener('click', () => {
            const card = btn.closest('.product-card');
            const productName = card.querySelector('h3').textContent;
            alert(`Você clicou em: ${productName}`);
        });
    });
    */
}

let cartCounter = 0;
let cartTotal = 0;

function updateCart(price) {
  cartCounter++;
  cartTotal += price;
  document.getElementById('cart-counter').innerText = cartCounter;
  document.getElementById('cart-total').innerText = `R$ ${cartTotal.toFixed(2)}`;
}