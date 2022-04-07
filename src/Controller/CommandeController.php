<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/voir-les-commandes", name="show_commandes", methods={"GET"})
     * @param CommandeRepository $commandeRepository
     * @return Response
     */
    public function showCommandes(CommandeRepository $commandeRepository): Response
    {
        return $this->render('admin/show_commandes.html.twig', [
            'commandes' => $commandeRepository->findBy(['deletedAt' => null])
        ]);
    }

    /**
     * @Route("/archiver-une-commande/{id}", name="soft_delete_commande", methods={"GET"})
     * @param Commande $commande
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function softDeleteCommande(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $commande->setDeletedAt(new DateTime());
        $commande->setState('annulée');

        $entityManager->persist($commande);
        $entityManager->flush();

        $this->addFlash('success', "La commande #". $commande->getId() ." a bien été annulée.");
        return $this->redirectToRoute('show_commandes');
    }

    /**
     * @Route("/voir-les-commandes-annulees", name="show_canceled_commandes", methods={"GET"})
     * @param CommandeRepository $commandeRepository
     * @return Response
     */
    public function showCanceledCommandes(CommandeRepository $commandeRepository): Response
    {
        // Nous utilisons la méthode créée dans le CommandeRepository pour récupérer les commandes annulées.
        $canceledCommandes = $commandeRepository->findByCanceled();

        return $this->render('admin/trash/show_canceled_commandes.html.twig', [
            'canceled_commandes' => $canceledCommandes
        ]);
    }

    /**
     * @Route("/restaure-une-commande/{id}", name="restore_commande", methods={"GET"})
     * @param Commande $commande
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function restoreCommande(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $commande->setDeletedAt(null);
        $commande->setState("en cours");

        $entityManager->persist($commande);
        $entityManager->flush();

        return $this->redirectToRoute('show_canceled_commandes');
    }

    /**
     * @Route("/supprimer-une-commande/{id}", name="hard_delete_commande", methods={"GET"})
     * @param Commande $commande
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function hardDeleteCommande(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($commande);
        $entityManager->flush();

        $this->addFlash('success', 'La commande a bien été supprimée.');
        return $this->redirectToRoute('show_canceled_commandes');
    }
}