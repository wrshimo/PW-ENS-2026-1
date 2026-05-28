document.addEventListener("DOMContentLoaded",()=>{let e=[],t="all",n=$(),i=document.getElementById("cart-icon"),o=document.getElementById("cart-count"),r=document.getElementById("name-search"),a=document.getElementById("price-filter"),c=document.getElementById("sort-order"),l=document.getElementById("filter-btn"),s=document.getElementById("product-list"),d=document.querySelectorAll(".category-filter");document.getElementById("contact-form");let u=document.getElementById("clear-cart-btn"),m=document.getElementById("confirm-clear-btn"),p=new bootstrap.Modal(document.getElementById("checkoutModal")),f=new bootstrap.Popover(i,{html:!0,sanitize:!1,trigger:"click",placement:"bottom",title:"Resumo do Carrinho",content:"Seu carrinho est\xe1 vazio."}),g=new bootstrap.Modal(document.getElementById("confirm-clear-cart-modal"));function v(e){let t=parseFloat(e.replace(/[^0-9,-]+/g,"").replaceAll(".","").replace(",","."));return Number.isFinite(t)?t:0}new bootstrap.Tooltip(u);let y=e=>e.toLocaleString("pt-BR",{style:"currency",currency:"BRL"});function h(){return n.items.reduce((e,t)=>e+t.qty,0)}function b(){return n.items.reduce((e,t)=>e+t.preco*t.qty,0)}function E(){localStorage.setItem("cart",JSON.stringify(n))}function $(){let e=localStorage.getItem("cart");if(!e)return{items:[]};try{let t=JSON.parse(e);if(!t||!Array.isArray(t.items))return{items:[]};return t}catch{return{items:[]}}}function I(){n={items:[]},E(),k(),g.hide()}function B(t){let i=e.find(e=>e.id===t);if(!i)return;let o=n.items.find(e=>e.id===t);o?o.qty+=1:n.items.push({id:i.id,nome:i.nome,preco:i.preco,qty:1}),E(),k()}function L(e){n.items=n.items.map(t=>t.id===e?{...t,qty:t.qty-1}:t).filter(e=>e.qty>0),E(),k()}function w(){if(0===n.items.length)return'<div class="text-muted">Seu carrinho est\xe1 vazio.</div>';let e=n.items.map(e=>`
    <div class="d-flex justify-content-between align-items-start gap-2 mb-2 cart-popover-item">
      <div class="cart-popover-item__info">
        <div class="fw-semibold cart-popover-item__name">${e.nome}</div>
        <div class="text-muted" style="font-size:0.8rem">${e.qty} \xd7 ${y(e.preco)}</div>
      </div>
      <button
        type="button"
        class="btn btn-sm btn-outline-danger cart-popover-item__remove remove-item"
        data-id="${e.id}"
        aria-label="Remover 1 unidade"
        title="Remover 1"
      >
        <i class="bi bi-dash"></i>
      </button>
    </div>
  `).join("");return`
<div class="cart-popover">
  ${e}
  <hr class="my-2" />
  <div class="d-flex justify-content-between">
    <span class="fw-semibold">Total</span>
    <span class="fw-bold">${y(b())}</span>
  </div>
  <div class="text-muted" style="font-size:0.8rem">Clique no “-” para remover 1 unidade.</div>
  <button id="go-to-checkout" class="btn btn-primary w-100">Finalizar Compra</button>
</div>
`}function k(){o.textContent=h(),f.setContent({".popover-body":w()}),u.disabled=0===n.items.length}function x(e){return`
<div class="col">
  <div class="card h-100 shadow-sm" data-product-id="${e.id}">
    <img src="${e.imagem}" class="card-img-top" alt="${e.nome}">
    <div class="card-body d-flex flex-column">
      <h5 class="card-title">${e.nome}</h5>
      <p class="card-text fw-semibold">${y(e.preco)}</p>

      <div class="product-details" id="details-${e.id}">
        <p class="mb-2">${e.descricao}</p>
        <span class="badge text-bg-light border">Categoria: ${e.categoria}</span>
      </div>

      <div class="mt-auto pt-3 d-flex gap-2 flex-wrap">
        <button class="btn btn-outline-primary btn-details" type="button">Ver detalhes</button>
        <button class="btn btn-success btn-add" type="button">Adicionar ao carrinho</button>
      </div>
    </div>
  </div>
</div>
`}function C(e){s.innerHTML=e.map(x).join("")}function _(e){let t=document.getElementById(`details-${e}`);t&&t.classList.toggle("is-open")}function q(){let n=[...e];"all"!==t&&(n=n.filter(e=>e.categoria===t));let i=r.value.toLowerCase();i&&(n=n.filter(e=>e.nome.toLowerCase().includes(i)));let o=parseFloat(a.value);Number.isNaN(o)||(n=n.filter(e=>e.preco<=o));let l=c.value;"default"!==l&&n.sort((e,t)=>"asc"===l?e.preco-t.preco:t.preco-e.preco),C(n)}function P(t){e=t.map(e=>({...e,preco:"string"==typeof e.preco?v(e.preco):e.preco})),q()}function j(){fetch("/api/produtos.php").then(e=>{if(!e.ok)throw Error("HTTP "+e.status);return e.json()}).then(P).catch(e=>{console.warn("API indispon\xedvel",e)})}function T(){fetch("/api/clientes.php").then(e=>e.json()).then(e=>{let t=document.getElementById("select-cliente");t.innerHTML='<option value="">Selecione um cliente...</option>'+e.map(e=>`<option value="${e.id}">${e.nome}</option>`).join("")}).catch(()=>alert("Erro ao carregar clientes. Verifique se est\xe1 logado."))}function A(){let e=new Date,t=e.getDay();switch(t){case 1:return{title:"Segunda Tech",desc:"10% OFF"};case 4:return{title:"Quinta do Look",desc:"30% OFF na segunda pe\xe7a"};default:return{title:"Promo\xe7\xe3o do Dia",desc:"Nenhuma promo\xe7\xe3o dispon\xedvel"}}}r.addEventListener("input",q),l.addEventListener("click",q),c.addEventListener("change",q),d.forEach(e=>{e.addEventListener("click",e=>{e.preventDefault(),t=e.target.getAttribute("data-category"),q()})}),s.addEventListener("click",e=>{let t=e.target,n=t.closest(".card");if(!n)return;document.querySelectorAll(".card").forEach(e=>e.classList.remove("selected")),n.classList.add("selected");let i=Number(n.dataset.productId);if(t.closest(".btn-details")){e.stopPropagation(),_(i);return}if(t.closest(".btn-add")){e.stopPropagation(),B(i);return}}),document.body.addEventListener("click",e=>{let t=e.target.closest(".remove-item");if(!t)return;e.preventDefault();let n=Number(t.dataset.id);L(n),f.update()}),u.addEventListener("click",()=>{g.show()}),m.addEventListener("click",I),document.body.addEventListener("click",e=>{if("go-to-checkout"===e.target.id){if(0===h())return alert("Carrinho vazio!");document.getElementById("checkout-total").textContent=y(b()),T(),p.show()}}),document.getElementById("btn-confirmar-pedido").addEventListener("click",()=>{let e=document.getElementById("select-cliente").value;if(!e)return alert("Por favor, selecione um cliente.");let t={cliente_id:e,total:b(),items:n.items};fetch("/api/pedidos.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(t)}).then(e=>e.json()).then(e=>{if(e.error)throw Error(e.error);alert(e.message),I(),p.hide()}).catch(e=>alert("Falha ao gravar pedido: "+e.message))});let S=A();function z(e){let t=[];for(let n=1;n<=e;n++)t.push({id:1e3+n,nome:`Produto ${n}`,preco:Math.round(5e5*Math.random())/100,imagem:`https://placehold.co/400x400?text=${n}`,categoria:n%2==0?"Eletr\xf4nicos":"Roupas",descricao:"Produto gerado para treino de arrays/DOM/eventos."});return t}document.getElementById("promo-title").textContent=S.title,document.getElementById("promo-desc").textContent=S.desc,document.getElementById("generate-fake-btn").addEventListener("click",()=>{let e=Number(document.getElementById("fake-count").value),t=z(e);P(t)}),document.getElementById("load-from-api-btn").addEventListener("click",()=>{j()}),"undefined"!=typeof produtosDoBanco&&Array.isArray(produtosDoBanco)&&produtosDoBanco.length>0?P(produtosDoBanco):j(),k()});