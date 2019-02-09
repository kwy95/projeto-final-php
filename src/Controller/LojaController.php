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
		$banco = new Banco();
		$ids = $banco->getIdsProdutos();
		$id1 = $ids[random_int(0, count($ids)-1)];
		$id2 = $ids[random_int(0, count($ids)-1)];
		while($id1 == $id2) {
			$id2 = $ids[random_int(0, count($ids)-1)];
		}

		$produto1 = $banco->getProduto($id1);
		$produto2 = $banco->getProduto($id2);

		return $this->render('loja/index.html.twig', [
			'produto1' => $produto1,
			'produto2' => $produto2
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
		if (is_array($carrinho) && array_key_exists($id, $carrinho)) {
			$quantidade = min($carrinho[$id]['quantidade'] + 1, $produto->getEstoque());
			$totalItem = $quantidade * $produto->getPreco();
			$carrinho[$id]['quantidade'] = $quantidade;
			$carrinho[$id]['total'] = $totalItem;
		}
		else {
			$totalItem = $produto->getPreco();
			$carrinho[$id] = array('produto' => $produto, 'quantidade' => 1, 'total' => $totalItem);
		}

		$total = 0;
		foreach ($carrinho as $item) {
			$total += $item['total'];
		}
		$carrinho[$id]['total'] = number_format($carrinho[$id]['total'], 2, ',', '.');
		$session->set('carrinho', $carrinho);
		$session->set('carrinho_total', number_format($total, 2, ',', '.'));

		return $this->redirectToRoute('app_loja_carrinho');
	}

	/**
	* @Route("/carrinho/{id}/alterar")
	*/
	public function carrinhoAlterar($id, Request $request, SessionInterface $session) {
		$banco = new Banco();
		$produto = $banco->getProduto($id);
		$quantidade = $request->request->get('quantidade');
		$carrinho = $session->get('carrinho');
		//$produto = $carrinho[$id]['produto'];
		//$carrinho[$id]['total'] = 0;
		if($quantidade == 0) {
			unset($carrinho[$id]);
		} else {
			$carrinho[$id]['quantidade'] = min($quantidade, $produto->getEstoque());
			$carrinho[$id]['total'] = $carrinho[$id]['quantidade'] * $produto->getPreco();
		}

		$total = 0;
		foreach ($carrinho as $item) {
			$total += $item['total'];
		}
		if(isset($carrinho[$id])) {
			$carrinho[$id]['total'] = number_format($carrinho[$id]['total'], 2, ',', '.');
		}
		$session->set('carrinho', $carrinho);
		$session->set('carrinho_total', number_format($total, 2, ',', '.'));

		return $this->redirectToRoute('app_loja_carrinho');
	}
}
