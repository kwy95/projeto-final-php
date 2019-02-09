<?php
namespace App\Repository;

use App\Entity\Produto;
use App\Entity\Cliente;

class Banco {
	private function conecta() {
		$host = 'localhost';
		$user = 'root';
		$pass = 'senha';
		$base = 'verao-2019';
		$banco = new \mysqli($host, $user, $pass, $base);
		if (\mysqli_connect_errno()) {
			exit("Não foi possível conectar no banco de dados!");
		}

		return $banco;
	}

	private function getResults($strsql) {
		$banco = $this->conecta();

		$statement = $banco->prepare($strsql);
		if (!$statement) {
			exit($banco->error);
		}

		if (!$statement->execute()) {
			exit($banco->error);
		}

		$resultados = $statement->get_result();

		return $resultados;
	}

	private function fetchProduto($linha) {
		$produto = new Produto();
		$produto->setId($linha->id);
		$produto->setNome($linha->nome);
		$produto->setDescricao($linha->descricao);
		$produto->setPreco($linha->preco);
		$produto->setEstoque($linha->estoque);
		$produto->setImagem($linha->imagem);

		return $produto;
	}

	private function fetchCliente($linha) {
		$cliente = new Cliente();
		$cliente->setId($linha->id);
		$cliente->setNome($linha->nome);
		$cliente->setEmail($linha->email);
		$cliente->setSenha($linha->senha);

		return $cliente;
	}

	public function getProduto($id) {
		$strsql = "select * from produtos where id = " . (int) $id;

		$resultados = $this->getResults($strsql);
		$linha = $resultados->fetch_object();
		$produto = $this->fetchProduto($linha);

		return $produto;
	}

	public function getProdutosByCategoria($nome) {
		$nome = trim($nome);
		$strsql = "select p.* from produtos as p
		inner join categorias as c on c.id = p.categoria_id
		where c.nome = '$nome'";

		$resultados = $this->getResults($strsql);

		$produtos = array();
		while ($linha = $resultados->fetch_object()) {
			$produtos[] = $this->fetchProduto($linha);
		}

		return $produtos;
	}

	public function getProdutosBusca($termos) {
		$produtos = array();
		foreach ($termos as $termo) {
			$strsql = "select * from produtos as p where nome like '%$termo%' or descricao like '%$termo%'";
			$resultados = $this->getResults($strsql);

			while ($linha = $resultados->fetch_object()) {
				$produto = $this->fetchProduto($linha);
				if(!isset($produtos[$produto->getId()])) {
					$produtos[$produto->getId()] = $produto;
				}
			}
		}

		return $produtos;
	}

	private function insert($tabela, $campos, $entry) {
		$sqlstr = "INSERT INTO $tabela (";
		foreach ($campos as $campo) {
			$sqlstr = $sqlstr . "$campo, ";
		}
		$sqlstr = substr($sqlstr, 0, -2) . ") values (";
		foreach($entry as $valor) {
			$sqlstr = $sqlstr . "'$valor', ";
		}
		$sqlstr = substr($sqlstr, 0, -2) . ")";
		$banco = $this->conecta();
		if(!$banco->query($sqlstr)) {
			echo $sqlstr . PHP_EOL;
			exit($banco->error);
		}

	}

	public function insertCliente($cliente) {
		$campos = [ 'nome', 'email', 'telefone', 'senha' ];
		$values = [ $cliente->getNome(), $cliente->getEmail(), $cliente->getTelefone(), $cliente->getSenha() ];
		/*$nome = $cliente->getNome();
		$email = $cliente->getEmail();
		$telefone = $cliente->getTelefone();
		$senha = $cliente->getSenha();
		$strsql = "INSERT INTO clientes (nome, email, telefone, senha) values ('$nome', '$email', '$telefone', '$senha')";

		if(!$this->query($strsql)) {
			exit($this->error);
		}*/
		$this->insert('clientes', $campos, $values);
	}

	public function login($email, $senha) {
		$senha = md5($senha);
		$strsql = "select * from clientes where email = '$email' and senha = '$senha'";

		$resultados = $this->getResults($strsql);

		$linha = $resultados->fetch_object();
		
		if (!$linha) {
			return false;
		}

		$cliente = $this->fetchCliente($linha);

		return $cliente;
	}

}
