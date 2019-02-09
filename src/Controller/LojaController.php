<?php
namespace App\Controller;

use App\Repository\Banco;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LojaController extends AbstractController {
	/**
	* @Route("/", name="app_loja_home")
	*/
	public function index() {
		return $this->render('loja/index.html.twig', [

		]);
	}

	/**
	* @Route("/loja/finalizar", name="app_loja_finalizar")
	*/
	public function finalizar(Request $request, SessionInterface $session) {
		$email = $request->request->get('email');
		$senha = $request->request->get('senha');

		$banco = new Banco();
		$cliente = $banco->login($email, $senha);

		$msg_erro = '';
		if ($cliente === false) {
			$msg_erro = 'Login e/ou senha inválido.';
		}
		else {
			$session->set('cliente', $cliente);
			return $this->redirectToRoute('app_loja_sucesso');
		}


		return $this->render('loja/finalizar.html.twig', [
			'msg_erro' => $msg_erro
		]);
	}

	/**
	* @Route("/loja/sucesso", name="app_loja_sucesso")
	*/
	public function sucesso(SessionInterface $session) {
		$carrinho = $session->get('carrinho');
		$total = $session->get('carrinho_total');
		$cliente = $session->get('cliente');

		$session->remove('carrinho');
		$session->remove('carrinho_total');

		return $this->render('loja/sucesso.html.twig', [
			'carrinho' => $carrinho,
			'total' => $total,
			'cliente' => $cliente
		]);
	}

	/**
	* @Route("/carrinho", name="app_loja_carrinho")
	*/
	public function carrinho(SessionInterface $session) {
		$carrinho = $session->get('carrinho');
		$total = $session->get('carrinho_total');
		if (!is_array($carrinho)) {
			$carrinho = array();
		}


		return $this->render('loja/carrinho.html.twig', [
			'carrinho' => $carrinho,
			'total' => $total
		]);
	}


	/**
	* @Route("/carrinho/{id}")
	*/
	public function carrinhoAdicionar($id, SessionInterface $session) {
		$banco = new Banco();
		$produto = $banco->getProduto($id);

		$carrinho = $session->get('carrinho');
		if (is_array($carrinho) && array_key_exists($produto->getId(), $carrinho)) {
			$quantidade = $carrinho[$produto->getId()]['quantidade'] + 1;
			$totalItem = $quantidade * $produto->getPreco();
			$carrinho[$produto->getId()]['quantidade'] = $quantidade;
			$carrinho[$produto->getId()]['total'] = $totalItem;
		}
		else {
			$totalItem = $produto->getPreco();
			$carrinho[$produto->getId()] = array('produto' => $produto, 'quantidade' => 1, 'total' => $totalItem);
		}

		$session->set('carrinho', $carrinho);

		$total = 0;
		foreach ($carrinho as $item) {
			$total += $item['total'];
		}
		$session->set('carrinho_total', $total);

		return $this->redirectToRoute('app_loja_carrinho');
	}
}
