<?php

require_once 'includes/init.php';
require_once 'includes/processar_acoes.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1> Carrinho de Compras</h1>
        
        <?php if (!empty($mensagem)): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; border: 1px solid #c3e6cb;">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="produtos">
            <h2>Produtos Disponiveis
                <form method="POST" style="display: inline; margin-left: 20px;">
                    <input type="hidden" name="acao" value="resetar_estoque">
                    <button type="submit" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">
                         Resetar Estoque
                    </button>
                </form>
            </h2>
            <?php require_once 'includes/exibir_produtos.php'; ?>
        </div>

        <div class="carrinho">
            <h2>Seu Carrinho 
                <?php if (!empty($itensCarrinho)): ?>
                    <span style="background: #3498db; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                        <?= $carrinho->getQuantidadeTotal() ?> itens
                    </span>
                <?php endif; ?>
            </h2>
            <?php require_once 'includes/exibir_carrinho.php'; ?>
            
            <?php if (!empty($itensCarrinho)): ?>
                <div class="carrinho-total">
                    Total: R$ <?= number_format($carrinho->calcularTotal($produtos), 2, ',', '.') ?>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="acao" value="comprar">
                        <button type="submit" class="btn-comprar">
                             Finalizar Compra
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>