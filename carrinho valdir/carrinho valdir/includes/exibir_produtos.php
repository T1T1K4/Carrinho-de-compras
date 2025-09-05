<?php
declare(strict_types=1);

// Exibir produtos
foreach ($produtos as $dadosProduto) {
    $produto = new Produto($dadosProduto['id'], $dadosProduto['nome'], $dadosProduto['preco'], $dadosProduto['estoque']);
    echo $produto->renderizarProduto();
}
