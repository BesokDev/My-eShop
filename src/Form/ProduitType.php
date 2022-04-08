<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('color', TextType::class, [
                'label' => 'Couleur',
            ])
            ->add('size', TextType::class, [
                'label' => 'Taille',
            ])
            ->add('collection', ChoiceType::class, [
                'label' => 'Collection',
                'choices' => [
                    'Collection Homme' => 'm',
                    'Collection Femme' => 'f',
                ]
            ])
            ->add('price', TextType::class, [
                'label' => 'Prix',
            ])
            ->add('stock', TextType::class, [
                'label' => 'Stock',
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo d\'illustration',
                'data_class' => null,
                'constraints' => [
                    new Image([
                       'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Les formats autorisés sont .jpg ou .png',
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Le poids maximal du fichier est : {{ limit }} {{ suffix }} ({{ name }}: {{ size }} {{ suffix }})',
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                // Parce que dans le ProduitController::updateProduit() nous avons passé une option à ce FormType,
                    # nous pouvons détecter l'action de l'utilisateur : création d'un nouveau produit OU modification d'un produit
                'label' => $options['photo'] ? 'Modifier' : 'Ajouter',
                'validate' => false,
                'attr' => [
                    'class' => 'd-block mx-auto col-3 my-3 btn btn-primary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'allow_file_upload' => true,
            'photo' => null,
        ]);
    }
}
