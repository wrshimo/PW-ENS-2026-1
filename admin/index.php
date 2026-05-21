<?php
require_once __DIR__ . '/../includes/layout.php';

render_head('Painel Admin - Produtos');
render_nav('admin');
?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Painel Administrativo</h1>
    <div>
      <button class="btn btn-danger" id="bulk-delete-btn" disabled>
        <i class="bi bi-trash"></i> Apagar Selecionados
      </button>
      <a class="btn btn-primary" href="/admin/novo.php"><i class="bi bi-plus-lg"></i> Novo produto</a>
    </div>
  </div>

  <div id="admin-alert"></div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead>
            <tr>
              <th class="text-center"><input class="form-check-input" type="checkbox" id="select-all-checkbox"></th>
              <th>ID</th>
              <th>Nome</th>
              <th>Categoria</th>
              <th class="text-end">Preço</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody id="produtos-tbody">
            <tr><td colspan="6" class="text-muted">Carregando...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Confirmação de Exclusão Única -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Confirmar Exclusão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- O texto será preenchido via JS -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirm-delete-btn">Confirmar Exclusão</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Confirmação de Exclusão em Massa -->
<div class="modal fade" id="confirmBulkDeleteModal" tabindex="-1" aria-labelledby="bulkModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkModalLabel">Confirmar Exclusão em Massa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Tem certeza que deseja apagar os produtos selecionados? Esta ação não pode ser desfeita.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirm-bulk-delete-btn">Apagar Selecionados</button>
      </div>
    </div>
  </div>
</div>

<script src="/admin/admin.js"></script>

<?php render_footer(); ?>