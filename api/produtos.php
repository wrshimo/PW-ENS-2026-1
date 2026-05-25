<?php
session_start();
$metodo = $_SERVER['REQUEST_METHOD'];

// Protege métodos de alteração
if (in_array($metodo, ['POST', 'PUT', 'DELETE'])) {
    if (!isset($_SESSION['logado'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Autenticação necessária']);
        exit;
    }
}

require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../includes/http_json.php';

// GET    /api/produtos.php            -> lista
// GET    /api/produtos.php?id=1       -> detalhe
// POST   /api/produtos.php            -> cria (campos via $_POST)
// PUT    /api/produtos.php?id=1       -> atualiza (campos via body)
// DELETE /api/produtos.php?id=1       -> exclui único
// DELETE /api/produtos.php            -> exclui em massa (campos via body: {"ids": [1,2,3]})

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
  if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id > 0) {
      $stmt = $pdo->prepare('SELECT id, nome, preco, categoria, imagem, descricao FROM produtos WHERE id = :id');
      $stmt->execute([':id' => $id]);
      $produto = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$produto) {
        json_response(['error' => 'Produto não encontrado'], 404);
      }

      json_response($produto);
    }

    $stmt = $pdo->query('SELECT id, nome, preco, categoria, imagem, descricao FROM produtos ORDER BY id DESC');
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    json_response($produtos);
  }

  if ($method === 'POST') {
    $data = $_POST;

    $missing = require_fields($data, ['nome', 'descricao', 'categoria', 'preco', 'imagem']);
    if ($missing) {
      json_response(['error' => 'Campos obrigatórios ausentes', 'missing' => $missing], 422);
    }

    $precoFiltrado = filter_var($data['preco'], FILTER_VALIDATE_FLOAT);
    if ($precoFiltrado === false) {
      json_response(['error' => 'Preço inválido'], 422);
    }

    $nome = htmlentities(trim($data['nome']), ENT_QUOTES, 'UTF-8');
    $descricao = htmlentities(trim($data['descricao']), ENT_QUOTES, 'UTF-8');
    $categoria = htmlentities(trim($data['categoria']), ENT_QUOTES, 'UTF-8');

    $urlFiltrado = filter_var(trim($data['imagem']), FILTER_VALIDATE_URL);
    if ($urlFiltrado === false) {
      json_response(['error' => 'URL da imagem inválido'], 422);
    }

    $stmt = $pdo->prepare(
      'INSERT INTO produtos (nome, descricao, categoria, preco, imagem)
       VALUES (:nome, :descricao, :categoria, :preco, :imagem)'
    );

    $stmt->execute([
      ':nome' => $nome,
      ':descricao' => $descricao,
      ':categoria' => $categoria,
      ':preco' => (float)$precoFiltrado,
      ':imagem' => $urlFiltrado,
    ]);

    if ($stmt->rowCount() === 0) {
      json_response(['error' => 'Falha ao inserir o produto'], 500);
    }

    $id = (int)$pdo->lastInsertId();
    json_response(['message' => 'Produto criado', 'id' => $id], 201);
  }

  if ($method === 'PUT') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
      json_response(['error' => 'Parâmetro id é obrigatório'], 400);
    }

    $data = get_request_body_params();

    $missing = require_fields($data, ['nome', 'descricao', 'categoria', 'preco', 'imagem']);
    if ($missing) {
      json_response(['error' => 'Campos obrigatórios ausentes', 'missing' => $missing], 422);
    }

    $precoFiltrado = filter_var($data['preco'], FILTER_VALIDATE_FLOAT);
    if ($precoFiltrado === false) {
      json_response(['error' => 'Preço inválido'], 422);
    }

    $nome = htmlentities(trim($data['nome']), ENT_QUOTES, 'UTF-8');
    $descricao = htmlentities(trim($data['descricao']), ENT_QUOTES, 'UTF-8');
    $categoria = htmlentities(trim($data['categoria']), ENT_QUOTES, 'UTF-8');

    $stmt = $pdo->prepare(
      'UPDATE produtos
         SET nome = :nome,
             descricao = :descricao,
             categoria = :categoria,
             preco = :preco,
             imagem = :imagem
       WHERE id = :id'
    );

    $stmt->execute([
      ':nome' => $nome,
      ':descricao' => $descricao,
      ':categoria' => $categoria,
      ':preco' => (float)$precoFiltrado,
      ':imagem' => trim($data['imagem']),
      ':id' => $id,
    ]);

    if ($stmt->rowCount() === 0) {
      json_response(['message' => 'Nenhuma alteração detectada', 'id' => $id]);
    }

    json_response(['message' => 'Produto atualizado com sucesso', 'id' => $id]);
  }

  if ($method === 'DELETE') {
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);

    // Exclusão em Massa
    if (isset($data['ids']) && is_array($data['ids'])) {
        $ids_to_delete = array_filter($data['ids'], fn($id) => is_numeric($id) && (int)$id > 0);
        
        if (empty($ids_to_delete)) {
            json_response(['error' => 'Nenhum ID válido fornecido para exclusão.'], 422);
        }
        
        $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));
        
        $stmt = $pdo->prepare("DELETE FROM produtos WHERE id IN ($placeholders)");
        $stmt->execute($ids_to_delete);
        
        json_response(null, 204);
    }

    // Exclusão Única
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM produtos WHERE id = :id');
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() === 0) {
            json_response(['error' => 'Produto não encontrado para exclusão'], 404);
        }

        json_response(null, 204);
    }

    // Nenhum cenário atendido
    json_response(['error' => 'Requisição inválida para DELETE. Forneça um `id` via GET ou um array de `ids` no corpo da requisição.'], 400);
  }

  json_response(['error' => 'Método não suportado'], 405);
} catch (PDOException $e) {
  error_log('API erro: ' . $e->getMessage());
  json_response(['error' => 'Erro interno ao acessar o banco'], 500);
}