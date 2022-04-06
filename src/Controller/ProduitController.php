<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/voir-les-produits", name="show_produit", methods={"GET"})
     */
    public function showProduit(ProduitRepository $produitRepository): Response
    {
        return $this->render("admin/show_produit.html.twig", [
            'produits' => $produitRepository->findAll(),
        ]);
    }


    /**
     * @Route("/creer-un-produit", name="create_produit", methods={"GET|POST"})
     */
    public function createProduit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $produit->setCreatedAt(new DateTime());
            $produit->setUpdatedAt(new DateTime());

            /** @var UploadedFile $photo */
            $photo = $form->get('photo')->getData();

            if($photo) {

                # guessExtension() devine l'extension du fichier À PARTIR du MimeType du fichier
                    #   => rappel : NE PAS confondre extension ET MimeType !
                $extension = '.' . $photo->guessExtension();
                $safeFilename = $slugger->slug($produit->getTitle());

                $newFilename = $safeFilename . '_' . uniqid() . $extension;

                try {
                    $photo->move($this->getParameter('uploads_dir'), $newFilename);
                    $produit->setPhoto($newFilename);
                } catch (FileException $exception) {
                    $this->addFlash('warning', 'La photo du produit ne s\'est pas importée avec succès. Veuillez réessayer en modifiant le produit.');
//                    return $this->redirectToRoute('show_produit');
                } // end catch()

            } // end if($photo)

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Le nouveau produit est en ligne avec succès !');
            return $this->redirectToRoute('show_produit');
        }// end if($form)

        return $this->render('admin/form/form_produit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifier-un-produit_{id}", name="update_produit", methods={"GET|POST"})
     */
    public function updateProduit(Produit $produit, EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger): Response
    {
        $originalPhoto = $produit->getPhoto();

        $form = $this->createForm(ProduitType::class, $produit, [
            'photo' => $originalPhoto
        ])->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $produit->setUpdatedAt(new DateTime());

            $photo = $form->get('photo')->getData();

            if($photo) {
                $this->handleFile($produit, $photo, $slugger);
            }
            else {
                $produit->setPhoto($originalPhoto);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez modifié le produit avec succès !');
            return $this->redirectToRoute('show_produit');
        }

        return $this->render('admin/form/form_produit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }



    ///////////////////////////////////////////////////////////////// PRIVATE FUNCTION /////////////////////////////////////////////////////////////

    private function handleFile(Produit $produit, UploadedFile $photo, SluggerInterface $slugger)
    {
        $extension = '.' . $photo->guessExtension();
        $safeFilename = $slugger->slug($produit->getTitle());

        $newFilename = $safeFilename . '_' . uniqid() . $extension;

        try {
            $photo->move($this->getParameter('uploads_dir'), $newFilename);
            $produit->setPhoto($newFilename);
        } catch (FileException $exception) {
            $this->addFlash('warning', 'La photo du produit ne s\'est pas importée avec succès. Veuillez réessayer en modifiant le produit.');
//                    return $this->redirectToRoute('show_produit');
        } // end catch()

//        return $produit;
    }



}