<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\UserOrder;
use App\Form\OrderType;
use App\Service\CartService;
use App\Repository\ArticleRepository;
use App\Repository\TicketRepository;
use App\Repository\UserOrderRepository;
use App\Repository\ReferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request, CartService $cartService, UserOrderRepository $userOrderRepository, TicketRepository $ticketRepository): Response
    {
        $orderForm = $this->createForm(OrderType::class);
        $orderForm->handleRequest($request);

        if ($orderForm->isSubmitted() && $orderForm->isValid()) {
            if(!$this->getUser()) return $this->redirectToRoute('app_login');
            if(!$this->getUser()->getFirstname() || !$this->getUser()->getLastname()) return $this->redirectToRoute('app_profile_update');
            if(!$this->getUser()->getAddresses()) return $this->redirectToRoute('app_profile_add_address');
            
            $order = new UserOrder();
            $order->setUser($this->getUser());
            $order->setTotal($cartService->getTotal());
            $userOrderRepository->add($order, true);

            $items = $cartService->getItems();
            foreach ($items as $item) {
                $detail = new Ticket();
                $detail->setReferenceOrder($order)
                ->setArticle($item['article'])
                ->setQty($item['qty']);
                $ticketRepository->add($detail, true);
            }
            $cartService->clearCart();
            $this->addFlash('success', 'Votre commande a bien été enregistrée !');
            return $this->redirectToRoute('app_profile_orders');
            
            //cartService->getItems()=>articles
            //create ticket for each article
            //redirect user commande is validated
        }
        return $this->render('cart/index.html.twig', [
            'articles' => $cartService->getItems(),
            'total' => $cartService->getTotal(),
            'orderForm' =>$orderForm->createView()
        ]);
    }

    #[Route('/cart/add', name: 'app_cart_add')]
    public function add(
        Request $request,
        ArticleRepository $articleRepository,
        CartService $cartService,
        ReferenceRepository $referenceRepository): Response 
        {
        $reference = $request->get('reference_id');
        $color = $request->get('color');
        $size = $request->get('size');
        $qty = $request->get('qty');

        $article = $articleRepository->findOneByParams($reference, $size, $color);
        $reference = $referenceRepository->find($reference);
        if ($article) {
            $stock = $article->getQty();
            if ($stock >= $qty && $stock > 0) {
                $cartService->add($article->getId(), $qty);
                $this->addFlash('success', 'Article ajouté au panier !');
                return $this->redirectToRoute('app_shop_show', [
                    'slug' => $reference->getSlug()
                ]);
            } else {
                $this->addFlash('danger', 'Stock insuffisant !');
                return $this->redirectToRoute('app_shop_show', [
                    'slug' => $reference->getSlug()]);
            }
        }
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id, CartService $cartService): Response
    {
        $cartService->remove($id);
        return $this->redirectToRoute('app_cart');
    }

    //completer gestion de stock
}
