<?php
namespace App\Entity;

class Cliente {
	protected $id;
	protected $nome;
	protected $email;
	protected $telefone;
	protected $senha;

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

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function getTelefone() {
		return $this->telefone;
	}

	public function setTelefone($telefone) {
		$digitos = '';
		for($i=0; $i<strlen($telefone); $i++) {
			if(is_numeric($telefone[$i])) {
				$digitos = $digitos . $telefone[$i];
			}
		}
		switch (strlen($digitos)) {
			case 11:
				$telefone = '(' . substr($digitos, 0, 2) . ') ' . substr($digitos, 2, 5) . '-' . substr($digitos, 7);
				break;
			case 10:
				$telefone = '(' . substr($digitos, 0, 2) . ') ' . substr($digitos, 2, 4) . '-' . substr($digitos, 6);
				break;
			case 9:
				$telefone = substr($digitos, 0, 5) . '-' . substr($digitos, 5);
				break;
			case 8:
				$telefone = substr($digitos, 0, 4) . '-' . substr($digitos, 4);
				break;
			default:
				$telefone = '';
				break;
		}
		$this->telefone = $telefone;
	}

	public function getSenha() {
		return $this->senha;
	}

	public function setSenha($senha) {
		$this->senha = md5($senha);
	}


}
