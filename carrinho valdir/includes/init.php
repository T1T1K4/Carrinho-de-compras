<?php
declare(strict_types=1);

session_start();
require_once 'classes/Produto.php';
require_once 'classes/Carrinho.php';

// Inicializar produtos e carrinho com persistencia de sessao
if (!isset($_SESSION['produtos']) || !isset($_SESSION['carrinho'])) {
    $_SESSION['produtos'] = Produto::obterProdutosPadrao();
    $_SESSION['carrinho'] = [];
}

$produtos = $_SESSION['produtos'];
$carrinho = new Carrinho();
$carrinho->carregarDaSessao($_SESSION['carrinho']);
