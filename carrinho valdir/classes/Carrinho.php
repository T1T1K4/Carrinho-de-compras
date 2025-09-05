<?php

declare(strict_types=1);

class Carrinho
{
    private array $itens = [];


    public function adicionarItem(int $idProduto, int $quantidade): bool
    {
        if ($quantidade <= 0) {
            return false;
        }

        if (isset($this->itens[$idProduto])) {
            $this->itens[$idProduto]['quantidade'] += $quantidade;
        } else {
            $this->itens[$idProduto] = [
                'id_produto' => $idProduto,
                'quantidade' => $quantidade
            ];
        }

        return true;
    }

    public function removerItem(int $idProduto): bool
    {
        if (isset($this->itens[$idProduto])) {
            unset($this->itens[$idProduto]);
            return true;
        }

        return false;
    }

    public function atualizarQuantidade(int $idProduto, int $quantidade): bool
    {
        if ($quantidade <= 0) {
            return $this->removerItem($idProduto);
        }

        if (isset($this->itens[$idProduto])) {
            $this->itens[$idProduto]['quantidade'] = $quantidade;
            return true;
        }

        return false;
    }

    public function listarItens(): array
    {
        return $this->itens;
    }

    public function calcularSubtotal(int $idProduto, array $produtos): float
    {
        if (!isset($this->itens[$idProduto])) {
            return 0.0;
        }

        $produto = $this->encontrarProduto($idProduto, $produtos);
        if (!$produto) {
            return 0.0;
        }

        return $produto->getPreco() * $this->itens[$idProduto]['quantidade'];
    }

    public function calcularTotal(array $produtos): float
    {
        $total = 0.0;

        foreach ($this->itens as $idProduto => $item) {
            $total += $this->calcularSubtotal($idProduto, $produtos);
        }

        return $total;
    }

    public function limparCarrinho(): void
    {
        $this->itens = [];
    }

    public function getQuantidadeItens(): int
    {
        return count($this->itens);
    }

    public function getQuantidadeTotal(): int
    {
        $total = 0;
        foreach ($this->itens as $item) {
            $total += $item['quantidade'];
        }
        return $total;
    }

    public function carregarDaSessao(array $itensSessao): void
    {
        $this->itens = [];
        foreach ($itensSessao as $idProduto => $item) {
            if (is_array($item) && isset($item['id_produto']) && isset($item['quantidade'])) {
                $this->itens[$idProduto] = $item;
            }
        }
    }

    public function salvarNaSessao(): array
    {
        return $this->itens;
    }

    public function finalizarCompra(array &$produtos): array
    {
        if (empty($this->itens)) {
            return ['sucesso' => false, 'erro' => 'Carrinho vazio'];
        }

        // Verificar se ha estoque suficiente para todos os itens
        $erros = [];
        foreach ($this->itens as $idProduto => $item) {
            $produto = $this->encontrarProduto($idProduto, $produtos);
            if (!$produto) {
                $erros[] = "Produto ID {$idProduto} nao encontrado";
                continue;
            }
            
            if (!$produto->temEstoque($item['quantidade'])) {
                $erros[] = "Estoque insuficiente para {$produto->getNome()}. Disponivel: {$produto->getEstoque()}, Solicitado: {$item['quantidade']}";
            }
        }
        
        if (!empty($erros)) {
            return ['sucesso' => false, 'erro' => implode(' | ', $erros)];
        }

        // Subtrair do estoque
        foreach ($this->itens as $idProduto => $item) {
            $produto = $this->encontrarProduto($idProduto, $produtos);
            $estoqueAnterior = $produto->getEstoque();
            $produto->diminuirEstoque($item['quantidade']);
            $estoqueNovo = $produto->getEstoque();
            
            // Atualizar o array de produtos original
            foreach ($produtos as &$produtoArray) {
                if ($produtoArray['id'] === $idProduto) {
                    $produtoArray['estoque'] = $estoqueNovo;
                    break;
                }
            }
        }

        $total = $this->calcularTotal($produtos);
        
        return [
            'sucesso' => true, 
            'total' => $total,
            'itens_comprados' => $this->itens
        ];
    }

    private function encontrarProduto(int $idProduto, array $produtos): ?Produto
    {
        foreach ($produtos as $dadosProduto) {
            if ($dadosProduto['id'] === $idProduto) {
                return new Produto(
                    $dadosProduto['id'],
                    $dadosProduto['nome'],
                    $dadosProduto['preco'],
                    $dadosProduto['estoque']
                );
            }
        }

        return null;
    }

    public function renderizarItemCarrinho(array $item, array $produtos): string
    {
        $produto = $this->encontrarProduto($item['id_produto'], $produtos);
        if (!$produto) {
            return '';
        }

        $subtotal = $this->calcularSubtotal($item['id_produto'], $produtos);
        $subtotalFormatado = number_format($subtotal, 2, ',', '.');
        $precoFormatado = number_format($produto->getPreco(), 2, ',', '.');

        return "
        <div class='carrinho-item'>
            <div>
                <strong>{$produto->getNome()}</strong><br>
                <small>R$ {$precoFormatado} x {$item['quantidade']} = R$ {$subtotalFormatado}</small>
                " . ($item['quantidade'] > 1 ? "<br><span style='color: #27ae60; font-size: 11px;'>✓ {$item['quantidade']} itens</span>" : "") . "
            </div>
            <div>
                <div style='font-weight: bold; margin-bottom: 5px; color: #2c3e50;'>R$ {$subtotalFormatado}</div>
                <form method='POST' style='display: inline;'>
                    <input type='hidden' name='acao' value='atualizar'>
                    <input type='hidden' name='id_produto' value='{$item['id_produto']}'>
                    <input type='number' name='quantidade' value='{$item['quantidade']}' min='1' max='{$produto->getEstoque()}' class='quantidade' style='width: 60px;' title='Alterar quantidade'>
                    <button type='submit' class='btn btn-success' style='padding: 4px 8px; font-size: 12px;'>Atualizar</button>
                </form>
                <form method='POST' style='display: inline; margin-left: 5px;'>
                    <input type='hidden' name='acao' value='remover'>
                    <input type='hidden' name='id_produto' value='{$item['id_produto']}'>
                    <button type='submit' class='btn btn-danger' style='padding: 4px 8px; font-size: 12px;'>Remover</button>
                </form>
            </div>
        </div>";
    }
}
