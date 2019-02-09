<?php
namespace App\Entity;

class Produto {
	protected $id;
	protected $nome;
	protected $descricao;
	protected $preco;
	protected $estoque;
	protected $imagem;


	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getNome() {
		return $this->nome;
	}

	public function setNome($nome) {
		$this->nome = $nome;
	}

	public function getDescricao() {
		return $this->descricao;
	}

	public function setDescricao($descricao) {
		$this->descricao = $descricao;
	}

	public function getPreco() {
		return $this->preco;
	}

	public function setPreco($preco) {
		$this->preco = $preco;
	}

	public function getEstoque() {
		return $this->estoque;
	}

	public function setEstoque($estoque) {
		$this->estoque = $estoque;
	}

	public function getImagem() {
		if ($this->imagem) {
			return $this->imagem;
		}
		return 'produto.png';
	}

	public function setImagem($imagem) {
		$this->imagem = $imagem;
	}
}
