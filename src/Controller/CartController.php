<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Produit;
use App\Entity\CartItems;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Entity\Cart;
use App\Form\CartType;
use App\Repository\CartRepository;
use App\Repository\CartItemsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_')]
    
    public function index(SessionInterface $session, EntityManagerInterface $entityManager, Request $request)
    {

        $session = $request->getSession();
        $sessionId = $session->getId();
        
        $cart = $this->getDoctrine()->getRepository(Cart::class)->findOneBy(['sessionId' => $sessionId]);
        
        if (!$cart) {
            $cart = new Cart();
            $cart->setSessionId($sessionId);
            $cart->setPrice(0);
            $cart->setQuantity(0);
            $entityManager->persist($cart);
            $entityManager->flush();
        }
        
        $products = [];
        $cartItems = $cart->getCartItems();
        
        foreach ($cartItems as $cartItem) {
            $products[$cartItem->getProduit()->getId()] = ['entity' => $cartItem, 'quantity' => $cartItem->getQuantity()];
        }
        
        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'products' => $products,
        ]);

    }
    #[Route('/add', name: 'cart_add')]
    public function add(EntityManagerInterface $entityManager, Request $request): Response
    {
        $session = $request->getSession();
        $sessionId = $session->getId();
    
        $productId = $request->query->get('id');
    
        $product = $entityManager->getRepository(Produit::class)->find($productId);
    
        if (!$product) {
            // Handle the case where the product does not exist, for example, return an error response.
            return $this->redirectToRoute('error_route'); // Adjust this to your actual error handling route
        }
    
        $existingCart = $entityManager->getRepository(Cart::class)->findOneBy(['sessionId' => $sessionId]);
    
        if (!$existingCart) {
            $existingCart = new Cart();
            $existingCart->setQuantity(1);
            $existingCart->setPrice($product->getPriu());
            $existingCart->setSessionId($sessionId);
            $entityManager->persist($existingCart);
        }
    
        $existingCartItem = $existingCart->getCartItems()->filter(function (CartItems $cartItem) use ($productId) {
            return $cartItem->getProduit()->getId() == $productId;
        })->first();
    
        if ($existingCartItem) {
            $existingCartItem->setQuantity($existingCartItem->getQuantity() + 1);
            // Assuming getProductPrice is a method to get the price. Replace with your actual logic.
            $existingCartItem->setPrice($existingCartItem->getQuantity() * $product->getPriu());
        } else {
            $existingCartItem = new CartItems();
            $existingCartItem->setProduit($product);
            $existingCartItem->setQuantity(1);
            $existingCartItem->setPrice($product->getPriu());
            $existingCart->addCartItem($existingCartItem);
            $entityManager->persist($existingCartItem); // Ensure new CartItems are persisted
        }
    
        $entityManager->flush();
    
        return $this->redirectToRoute('cart_'); // Adjust to your actual route for showing the cart
    }

    #[Route('cart/remove', name: 'cart_remove')]
    public function remove(ProduitRepository $produitRepository, SessionInterface $session, EntityManagerInterface $entityManager, Request $request)
    {

        $session = $request->getSession();
        $sessionId = $session->getId();
    
        $productId = $request->query->get('id');
    
        $existingCart = $entityManager->getRepository(Cart::class)->findOneBy(['sessionId' => $sessionId]);

        $existingCartItem = $existingCart->getCartItems()->filter(function (CartItems $cartItem) use ($productId) {
            return $cartItem->getProduit()->getId() == $productId;
        })->first();
    
        if ($existingCartItem) {
            if ($existingCartItem->getQuantity() == 1) {
                $existingCart->removeCartItem($existingCartItem);
            } else {
                $existingCartItem->setQuantity($existingCartItem->getQuantity() - 1);
            }}
            $entityManager->flush();
        
    
        return $this->redirectToRoute('cart_');
    }

    #[Route('cart/delete', name: 'cart_delete')]
    public function delete(EntityManagerInterface $entityManager, Request $request)
    {

        $session = $request->getSession();
        $sessionId = $session->getId();

        $cartItemid = $request->query->get('id');

        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['sessionId' => $sessionId]);

        if ($cartItem = $entityManager->getRepository(CartItems::class)->findOneBy(['id' => $cartItemid])) {
            $cart->removeCartItem($cartItem);
            $entityManager->remove($cartItem);
        
            if ($cart->getCartItems()->isEmpty()) {
                $entityManager->remove($cart);
            }

        $entityManager->flush();
}

return $this->redirectToRoute('cart_');
    }

    #[Route('cart/empty', name: 'cart_empty')]
    public function empty(SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $sessionId = $session->getId();
    
        // Find the cart associated with the session ID
        $cart = $entityManager->getRepository(Cart::class)->findOneBy(['sessionId' => $sessionId]);
    
        // If cart exists, remove all cart items and the cart itself
        if ($cart) {
            foreach ($cart->getCartItems() as $cartItem) {
                $entityManager->remove($cartItem);
            }
            $entityManager->remove($cart);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('cart_');
    }
    



}
