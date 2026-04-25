<?php
include_once('logica_desconto.php');

// Inicialza variáveis
$precoOriginal = 100;
$desconto = 20;

// Verifico se valores foram passados via HTTP/GET
if (isset($_GET['preco'])) {
    $precoOriginal = $_GET['preco'];
}
if (isset($_GET['desconto'])) {
    $desconto = $_GET['desconto'];
}

$precoFinal = calculaDesconto($precoOriginal, $desconto);
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
    <div class="container mt-5">
        <div class="card">
            <h3>Simulador de Descontos</h3>
            <p>Preço original: R$ <?= $precoOriginal ?></p>
            <p>Desconto: <?= $desconto ?>%</p>
            <p>Preço final: <span class="text-primary;">R$ <?= $precoFinal ?></span></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>