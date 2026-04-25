<?php
require_once 'processa_pedido.php';

$valMinFrete = 5000;

$meuCarrinho = [
    ['nome'=>'Notebook Usado', 'preco'=>3000, 'qnt'=>1],
    ['nome'=>'Monitor', 'preco'=>800, 'qnt'=>2],
];

// Busca item na Query String
if (isset($_GET['nome']) && isset($_GET['preco'])  && isset($_GET['qnt'])) {
  $meuCarrinho[] = ['nome'=>$_GET['nome'], 'preco'=>$_GET['preco'], 'qnt'=>$_GET['qnt']];
}

$totalFatura = calculaTotal($meuCarrinho);
$isGratis = temFreteGratis($totalFatura, $valMinFrete);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saudação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Produto</th>
        <th>Preço Unitário</th>
        <th>Quantidade</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($meuCarrinho as $item): ?>
      <tr>
        <td><?= $item['nome'] ?></td>
        <td><?= $item['preco'] ?></td>
        <td><?= $item['qnt'] ?></td>
        <td>R$ <?= subtotalItem($item['preco'], $item['qnt']) ?></td>
      </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="3" style="text-align: right;"><strong>Subtotal:</strong></td>
        <td>R$ <?= $totalFatura ?></td>
      </tr>
    </tbody>
  </table>

  <?php if ($isGratis): ?>
    <div class="alert alert-success">Frete Grátis Liberado!</div>
  <?php else: ?>
    <div class="alert alert-warning">Adicione mais itens para frete grátis!<br>
      Faltam apenas R$ <?= $valMinFrete - $totalFatura ?>.</div>
  <?php endif; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>