document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.getElementById('produtos-tbody');
  const alertBox = document.getElementById('admin-alert');

  // ========================================================
  // Seleção de Elementos para Exclusão em Massa
  // ========================================================
  const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
  const selectAllCheckbox = document.getElementById('select-all-checkbox');

  // ========================================================
  // Modais do Bootstrap
  // ========================================================
  const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
  const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
  const modalBody = document.querySelector('#confirmDeleteModal .modal-body');
  let productIdToDelete = null;

  const confirmBulkDeleteModal = new bootstrap.Modal(document.getElementById('confirmBulkDeleteModal'));
  const confirmBulkDeleteBtn = document.getElementById('confirm-bulk-delete-btn');

  // ========================================================
  // Funções Utilitárias
  // ========================================================
  const formatBRL = (value) =>
    Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

  function showAlert(type, message) {
    alertBox.innerHTML = `
      <div class="alert alert-${type}">${message}</div>
    `;
  }

  function renderRows(produtos) {
    if (!Array.isArray(produtos) || produtos.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" class="text-muted">Nenhum produto cadastrado.</td></tr>';
      return;
    }

    tbody.innerHTML = produtos
      .map(
        (p) => `
        <tr id="produto-${p.id}">
          <td class="text-center"><input class="form-check-input product-checkbox" type="checkbox" data-id="${p.id}"></td>
          <td>${p.id}</td>
          <td class="produto-nome">${p.nome}</td>
          <td>${p.categoria}</td>
          <td class="text-end">${formatBRL(p.preco)}</td>
          <td>
            <a class="btn btn-sm btn-outline-primary" href="/admin/editar.php?id=${p.id}">
              <i class="bi bi-pencil-square"></i> Editar
            </a>
            <button class="btn btn-sm btn-outline-danger btn-excluir" data-id="${p.id}" data-nome="${p.nome}">
              <i class="bi bi-trash"></i> Excluir
            </button>
          </td>
        </tr>
      `
      )
      .join('');
  }

  // ========================================================
  // Lógica da API
  // ========================================================
  function loadProdutos() {
    fetch('/api/produtos.php')
      .then((r) => r.json())
      .then(renderRows)
      .catch((err) => {
        console.error(err);
        showAlert('danger', 'Falha ao carregar produtos via API.');
      });
  }

  function deleteProduct() {
    if (!productIdToDelete) return;

    fetch(`/api/produtos.php?id=${productIdToDelete}`, { method: 'DELETE' })
      .then(r => {
        if (!r.ok) throw new Error('Falha na resposta da rede.');
        if (r.status === 204) return null;
        return r.json();
      })
      .then(data => {
        if (data && data.error) throw new Error(data.error);
        showAlert('success', 'Produto excluído com sucesso!');
        confirmDeleteModal.hide();
        loadProdutos();
      })
      .catch(err => {
        showAlert('danger', `Erro ao excluir produto: ${err.message}`)
        confirmDeleteModal.hide();
      })
      .finally(() => {
        productIdToDelete = null;
      });
  }

  function bulkDeleteProducts() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) return;

    fetch(`/api/produtos.php`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ ids: selectedIds })
    })
    .then(r => {
        if (!r.ok) throw new Error('Falha na resposta da rede.');
        if (r.status === 204) return null;
        return r.json();
    })
    .then(data => {
        if (data && data.error) throw new Error(data.error);
        showAlert('success', `${selectedIds.length} produtos foram excluídos com sucesso!`);
        confirmBulkDeleteModal.hide();
        loadProdutos();
        updateBulkDeleteButton();
    })
    .catch(err => {
        showAlert('danger', `Erro ao excluir produtos: ${err.message}`)
        confirmBulkDeleteModal.hide();
    });
  }

  // ========================================================
  // Lógica de Seleção de Checkboxes
  // ========================================================
  function getSelectedIds() {
      return [...document.querySelectorAll('.product-checkbox:checked')].map(cb => cb.dataset.id);
  }

  function updateBulkDeleteButton() {
    const selectedCount = getSelectedIds().length;
    bulkDeleteBtn.disabled = selectedCount === 0;
    if (selectedCount > 0) {
        bulkDeleteBtn.textContent = `Apagar Selecionados (${selectedCount})`;
    } else {
        bulkDeleteBtn.innerHTML = `<i class="bi bi-trash"></i> Apagar Selecionados`;
    }
  }

  // ========================================================
  // Eventos
  // ========================================================

  // Carregamento inicial
  loadProdutos();

  // Evento para abrir modal de exclusão única
  tbody.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-excluir');
    if (btn) {
        const id = btn.getAttribute('data-id');
        const nome = btn.getAttribute('data-nome');
        productIdToDelete = id;
        modalBody.textContent = `Tem certeza que deseja excluir o produto "${nome}"?`;
        confirmDeleteModal.show();
        return;
    }
  });
  
  // Eventos para checkboxes
  tbody.addEventListener('change', (e) => {
    if (e.target.classList.contains('product-checkbox')) {
        updateBulkDeleteButton();
        selectAllCheckbox.checked = getSelectedIds().length === document.querySelectorAll('.product-checkbox').length;
    }
  });

  // Evento para o botão de confirmação de exclusão única
  confirmDeleteBtn.addEventListener('click', deleteProduct);

  // Eventos para o modal de exclusão em massa
  bulkDeleteBtn.addEventListener('click', () => {
      if (getSelectedIds().length > 0) {
          confirmBulkDeleteModal.show();
      }
  });

  confirmBulkDeleteBtn.addEventListener('click', bulkDeleteProducts);

  // Evento para o checkbox "selecionar todos"
  selectAllCheckbox.addEventListener('change', (e) => {
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.checked = e.target.checked;
    });
    updateBulkDeleteButton();
  });

});