<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/mon-panier")
 */
class PanierController extends AbstractController
{
    /**
     * @Route("/voir-mon-panier", name="show_panier", methods={"GET"})
     * @param SessionInterface $session
     * @param ProduitRepository $produitRepository
     * @return Response
     */
    public function showPanier(SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $panier = $session->get('panier', []);

        return $this->render("panier/show_panier.html.twig");
    }
}