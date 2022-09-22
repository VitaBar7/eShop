<?php

namespace App\Controller;


use App\Service\CartService;
use App\Repository\ArticleRepository;
use App\Repository\ReferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'articles' => $cartService->getItems(),
            'total' => $cartService->getTotal(),
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
}
