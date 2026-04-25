<?php
function calculaDesconto($precoBase, $porcentagem) {
    $fator = (100 - $porcentagem) / 100;
    $precoComDesconto = $fator * $precoBase;
    return $precoComDesconto;
}