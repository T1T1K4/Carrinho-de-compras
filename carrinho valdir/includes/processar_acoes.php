<?php
declare(strict_types=1);

// Processar acoes do carrinho
$mensagem = '';
if ($_POST) {
    $acao = $_POST['acao'] ?? '';
    $idProduto = (int)($_POST['id_produto'] ?? 0);
    $quantidade = (int)($_POST['quantidade'] ?? 1);

    switch ($acao) {
        case 'adicionar':
            // Verificar se a quantidade solicitada e maior que o estoque
            $produto = array_filter($produtos, fn($p) => $p['id'] === $idProduto);
            $produto = reset($produto);
            
            if ($quantidade <= 0) {
                $mensagem = " Quantidade deve ser maior que zero!";
            } elseif ($quantidade > $produto['estoque']) {
                $mensagem = " Quantidade solicitada ({$quantidade}) e maior que o estoque disponivel! So temos {$produto['estoque']} unidades de {$produto['nome']}.";
            } else {
                if ($carrinho->adicionarItem($idProduto, $quantidade)) {
                    $mensagem = " {$quantidade}x {$produto['nome']} adicionado(s) ao carrinho!";
                } else {
                    $mensagem = " Erro ao adicionar item ao carrinho!";
                }
            }
            break;
            
        case 'remover':
            $carrinho->removerItem($idProduto);
            $mensagem = " Item removido do carrinho!";
            break;
            
        case 'atualizar':
            $carrinho->atualizarQuantidade($idProduto, $quantidade);
            $mensagem = " Quantidade atualizada!";
            break;
            
        case 'comprar':
            $resultado = $carrinho->finalizarCompra($produtos);
            if ($resultado['sucesso']) {
                $mensagem = " Compra realizada com sucesso! Total: R$ " . number_format($resultado['total'], 2, ',', '.');
                $carrinho->limparCarrinho();
                // Salvar produtos atualizados na sessao
                $_SESSION['produtos'] = $produtos;
                // Atualizar a variavel local tambem
                $produtos = $_SESSION['produtos'];
                
                // Debug: mostrar estoque atualizado
                $mensagem .= "<br><small>Estoque atualizado na sessao!</small>";
            } else {
                $mensagem = " Erro: " . $resultado['erro'];
            }
            break;
            
        case 'resetar_estoque':
            $_SESSION['produtos'] = Produto::obterProdutosPadrao();
            $produtos = $_SESSION['produtos'];
            $mensagem = " Estoque resetado para valores iniciais!";
            break;
    }
    
    // Salvar carrinho na sessao apos cada acao
    $_SESSION['carrinho'] = $carrinho->listarItens();
}
