<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/mon-compte", name="show_account", methods={"GET"})
     * @return Response
     */
    public function showAccount(CommandeRepository $commandeRepository): Response
    {
        $commands = $commandeRepository->findBy(['user' => $this->getUser()]);

        return $this->render('account/show_account.html.twig', [
            'commands' => $commands
        ]);
    }
}