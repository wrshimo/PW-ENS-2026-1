<?php
/**
 * api/pedidos.php
 * Endpoint para gestão de vendas
 */
require_once __DIR__ . '/../conexao.php';
require_once __DIR__ . '/../includes/http_json.php';

session_start();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Segurança: Apenas usuários logados podem acessar a API de pedidos
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    json_response(['error' => 'Acesso negado. Faça login.'], 401);
}

try {
    // GET: Retorna a lista de todos os pedidos e seus itens
    if ($method === 'GET') {
        // 1. Consulta para obter todos os pedidos com detalhes do cliente
        $stmtPedidos = $pdo->query('
            SELECT 
                p.id, 
                p.data_pedido, 
                p.status, 
                p.total, 
                c.nome AS cliente_nome, 
                c.email AS cliente_email
            FROM pedidos p
            JOIN clientes c ON p.cliente_id = c.id
            ORDER BY p.data_pedido DESC
        ');
        $pedidos = $stmtPedidos->fetchAll(PDO::FETCH_ASSOC);

        // 2. Para cada pedido, busca os itens correspondentes
        $stmtItens = $pdo->prepare('
            SELECT 
                pi.quantidade, 
                pi.preco_unitario,
                pr.nome AS produto_nome,
                pr.imagem AS produto_imagem
            FROM pedido_itens pi
            JOIN produtos pr ON pi.produto_id = pr.id
            WHERE pi.pedido_id = :pedido_id
        ');

        foreach ($pedidos as $key => $pedido) {
            $stmtItens->execute(['pedido_id' => $pedido['id']]);
            $pedidos[$key]['itens'] = $stmtItens->fetchAll(PDO::FETCH_ASSOC);
        }

        json_response($pedidos, 200);
    }
    
    // POST: Grava o pedido e os itens (Transação Atômica)
    if ($method === 'POST') {
        $data = get_request_body_params();

        if (!isset($data['cliente_id']) || empty($data['items'])) {
            json_response(['error' => 'Dados do pedido incompletos'], 422);
        }

        $pdo->beginTransaction();

        // 1. Grava Cabeçalho
        $stmt = $pdo->prepare('INSERT INTO pedidos (cliente_id, total) VALUES (:c, :t)');
        $stmt->execute([
            ':c' => (int)$data['cliente_id'],
            ':t' => (float)$data['total']
        ]);
        $pedidoId = $pdo->lastInsertId();

        // 2. Grava Itens em Loop
        $stmtItem = $pdo->prepare('
            INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario) 
            VALUES (:pid, :prod, :qty, :pre)
        ');

        foreach ($data['items'] as $item) {
            $stmtItem->execute([
                ':pid'  => $pedidoId,
                ':prod' => $item['id'],
                ':qty'  => $item['qty'],
                ':pre'  => $item['preco']
            ]);
        }

        $pdo->commit();
        json_response(['message' => 'Pedido #'.$pedidoId.' cadastrado com sucesso!', 'id' => $pedidoId], 201);
    }

    json_response(['error' => 'Método não suportado'], 405);
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_response(['error' => 'Falha no servidor: ' . $e->getMessage()], 500);
}