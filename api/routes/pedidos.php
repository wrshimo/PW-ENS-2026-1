<?php
/**
 * api/routes/pedidos.php
 * Rota para gestão de pedidos
 */
require_once __DIR__ . '/../../conexao.php';
require_once __DIR__ . '/../../includes/http_json.php';
require_once __DIR__ . '/../models/Pedido.php';

session_start();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Segurança: Apenas usuários logados podem acessar a API de pedidos
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    json_response(['error' => 'Acesso negado. Faça login.'], 401);
}

try {
    $pedidoModel = new Pedido($pdo);

    if ($method === 'GET') {
        $pedidos = $pedidoModel->getAll();
        json_response($pedidos, 200);
    } elseif ($method === 'POST') {
        $data = get_request_body_params();
        $result = $pedidoModel->create($data);
        json_response($result, 201);
    } else {
        json_response(['error' => 'Método não suportado'], 405);
    }
} catch (InvalidArgumentException $e) {
    json_response(['error' => $e->getMessage()], 422);
} catch (Exception $e) {
    json_response(['error' => 'Falha no servidor: ' . $e->getMessage()], 500);
}
