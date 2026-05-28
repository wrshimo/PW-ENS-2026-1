<?php
/**
 * api/models/Pedido.php
 * Modelo para manipulação de dados de pedidos
 */
class Pedido {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Retorna a lista de todos os pedidos e seus itens
     */
    public function getAll() {
        // 1. Consulta para obter todos os pedidos com detalhes do cliente
        $stmtPedidos = $this->pdo->query('
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
        $stmtItens = $this->pdo->prepare('
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

        return $pedidos;
    }

    /**
     * Grava um novo pedido e seus itens
     */
    public function create(array $data) {
        if (!isset($data['cliente_id']) || empty($data['items'])) {
            throw new InvalidArgumentException('Dados do pedido incompletos');
        }

        $this->pdo->beginTransaction();

        try {
            // 1. Grava Cabeçalho
            $stmt = $this->pdo->prepare('INSERT INTO pedidos (cliente_id, total) VALUES (:c, :t)');
            $stmt->execute([
                ':c' => (int)$data['cliente_id'],
                ':t' => (float)$data['total']
            ]);
            $pedidoId = $this->pdo->lastInsertId();

            // 2. Grava Itens em Loop
            $stmtItem = $this->pdo->prepare('
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

            $this->pdo->commit();
            return ['message' => 'Pedido #'.$pedidoId.' cadastrado com sucesso!', 'id' => $pedidoId];
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }
}