<?php

declare(strict_types=1);


class Produto
{
    private int $id;
    private string $nome;
    private float $preco;
    private int $estoque;

    public function __construct(int $id, string $nome, float $preco, int $estoque)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->preco = $preco;
        $this->estoque = $estoque;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getPreco(): float
    {
        return $this->preco;
    }

    public function getEstoque(): int
    {
        return $this->estoque;
    }

    public function temEstoque(int $quantidade): bool
    {
        return $this->estoque >= $quantidade;
    }

    public function diminuirEstoque(int $quantidade): void
    {
        if ($this->temEstoque($quantidade)) {
            $this->estoque -= $quantidade;
        }
    }

    public function aumentarEstoque(int $quantidade): void
    {
        $this->estoque += $quantidade;
    }

    public function renderizarProduto(): string
    {
        $precoFormatado = number_format($this->preco, 2, ',', '.');
        
        return "
        <div class='produto'>
            <div class='produto-info'>
                <div class='produto-nome'>{$this->nome}</div>
                <div class='produto-preco'>R$ {$precoFormatado}</div>
                <div class='produto-estoque'>Estoque: {$this->estoque}</div>
            </div>
            <div class='produto-actions'>
                <form method='POST' style='display: inline;'>
                    <input type='hidden' name='acao' value='adicionar'>
                    <input type='hidden' name='id_produto' value='{$this->id}'>
                    <label style='font-size: 12px; color: #666;'>Qtd:</label>
                    <input type='number' name='quantidade' value='1' min='1' max='{$this->estoque}' class='quantidade' title='Maximo: {$this->estoque} unidades'>
                    <button type='submit' class='btn btn-primary'>Adicionar</button>
                </form>
            </div>
        </div>";
    }

    public static function obterProdutosPadrao(): array
    {
        return [
            ['id' => 1, 'nome' => 'Camiseta', 'preco' => 59.90, 'estoque' => 10],
            ['id' => 2, 'nome' => 'Calca Jeans', 'preco' => 129.90, 'estoque' => 5],
            ['id' => 3, 'nome' => 'Tenis', 'preco' => 199.90, 'estoque' => 3],
            ['id' => 4, 'nome' => 'Bone', 'preco' => 39.90, 'estoque' => 8],
            ['id' => 5, 'nome' => 'Relogio', 'preco' => 299.90, 'estoque' => 2]
        ];
    }
}
