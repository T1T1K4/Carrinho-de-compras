<?php
declare(strict_types=1);

$itensCarrinho = $carrinho->listarItens();

if (empty($itensCarrinho)) {
    echo '<div class="carrinho-vazio">Carrinho vazio</div>';
} else {
    foreach ($itensCarrinho as $item) {
        echo $carrinho->renderizarItemCarrinho($item, $produtos);
    }
}
