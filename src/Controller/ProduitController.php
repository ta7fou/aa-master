<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class ProduitController extends AbstractController
{
    #[Route('/shop', name: 'app_produit')]
    public function index(): Response
    {
        $produit = $this->getDoctrine()->getRepository(produit::class)->findAll();
        return $this->render('produit/index.html.twig', [
            'produit' => $produit,
        ]);
    }
    
    #[Route('/shop/new', name: 'app_produit_new')]
    
    public function new(Request $request): Response
    {
        $produit = new produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_new');
        }

        return $this->render('back/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    #[Route('shop/editp/{id}', name: 'edit_produit')]
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_produit');
        }

        return $this->render('back/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/shop/remove/{id}', name: 'remove_produit')]
    public function delete($id, EntityManagerInterface $entityManager, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);   // Retrieve the animal to be removed

        if (!$produit) {
            throw $this->createNotFoundException('product not found');
        }

        $entityManager->remove($produit);         // Perform the removal 
        $entityManager->flush();

        return $this->redirectToRoute('app_produit');
    }
    


}
