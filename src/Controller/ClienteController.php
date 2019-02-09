<?php
namespace App\Controller;

use App\Entity\Cliente;
use App\Repository\Banco;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;

class ClienteController extends AbstractController {
	/** 
	* @Route("/cliente/form")
	*/
	public function new(Request $request) {
		$nome = $request->request->get('nome', '');
		$email = $request->request->get('email', '');
		$telefone = $request->request->get('telefone', '');
		$senha = $request->request->get('senha', '');
		$senhaConfirmacao = $request->request->get('senhaConfirmacao', '');

		$cliente = new Cliente();
		//$cliente->setId(1);
		$cliente->setNome($nome);
		$cliente->setEmail($email);
		$cliente->setTelefone($telefone);
		$cliente->setSenha($senha);

		$erros = array();
		if ($_POST) {
			if (!$nome) {
				$erros[] = 'Digite o nome!';
			}

			if (!$email) {
				$erros[] = 'Digite o e-mail!';
			}

			if (strlen($senha) < 6) {
				$erros[] = 'Digite uma senha com pelo 6 caracteres.';
			}
			elseif ($senha != $senhaConfirmacao) {
				$erros[] = 'A confirmação está diferente da senha.';
			}

			if (count($erros) == 0) {
				//$senha = $cliente->getSenha();
				$banco = new Banco();
				$banco->insertCliente($cliente);
				//echo $strsql;
				return $this->redirectToRoute('app_loja_finalizar');
			}
		}


		return $this->render('cliente/form.html.twig', [
			'cliente' => $cliente,
			'erros' => $erros
		]);
	}

}
