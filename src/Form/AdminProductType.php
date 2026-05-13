<?php

namespace App\Form; // on est dans le dossier Form, Symfony s'attend à ce namespace ici

use App\Entity\Product; // on importe l'entité Product pour que le formulaire soit lié à cet objet
use Symfony\Component\Form\AbstractType; // la classe de base de tous les formulaires Symfony
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // pour faire un menu déroulant (liste de choix)
use Symfony\Component\Form\Extension\Core\Type\IntegerType; // pour un champ nombre entier (ex: la quantité)
use Symfony\Component\Form\Extension\Core\Type\NumberType; // pour un champ nombre décimal (ex: le prix)
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // pour un grand champ texte (ex: la description)
use Symfony\Component\Form\Extension\Core\Type\TextType; // pour un simple champ texte (ex: le nom, le SKU)
use Symfony\Component\Form\FormBuilderInterface; // pour construire le formulaire champ par champ
use Symfony\Component\OptionsResolver\OptionsResolver; // pour configurer les options du formulaire (ex: quelle classe est liée)
use Symfony\Component\Validator\Constraints\Length; // pour valider la longueur minimale/maximale d'un champ texte
use Symfony\Component\Validator\Constraints\NotBlank; // pour obliger l'utilisateur à remplir un champ
use Symfony\Component\Validator\Constraints\Positive; // pour vérifier qu'un nombre est strictement positif (> 0)
use Symfony\Component\Validator\Constraints\PositiveOrZero; // pour vérifier qu'un nombre est >= 0 (pour la quantité)
use Symfony\Component\Validator\Constraints\Range; // pour vérifier qu'une valeur est dans un intervalle (ex: TVA entre 0 et 1)

// Ce formulaire AdminProductType sert à créer ET modifier un produit depuis l'interface admin.
// Il est lié à l'entité Product, donc chaque champ correspond à une propriété de Product.
class AdminProductType extends AbstractType
{
    // La méthode buildForm() est celle où on déclare tous les champs du formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ SKU : la référence unique du produit (ex: APL-IP16P)
            ->add('sku', TextType::class, [
                'label' => 'SKU (référence unique)', // texte affiché au dessus du champ
                'attr' => [
                    'placeholder' => 'Ex: APL-IP16P', // texte grisé à l'intérieur du champ
                    'class' => 'form-control', // classe Bootstrap pour le style
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le SKU est obligatoire.']), // on refuse si vide
                    new Length(['max' => 50, 'maxMessage' => 'Le SKU ne peut pas dépasser 50 caractères.']),
                ],
            ])

            // Champ Nom : le nom visible du produit (ex: iPhone 16 Pro)
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'placeholder' => 'Ex: iPhone 16 Pro',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire.']),
                    new Length(['max' => 255, 'maxMessage' => 'Le nom ne peut pas dépasser 255 caractères.']),
                ],
            ])

            // Champ Prix HT : le prix hors taxes, sans la TVA (ex: 999.17)
            // On utilise 'mapped' => false pour gérer la conversion manuellement dans le contrôleur,
            // car l'entité stocke prix_ht en string et non en float
            ->add('prix_ht', NumberType::class, [
                'label' => 'Prix HT (€)',
                'scale' => 2, // on affiche 2 chiffres après la virgule
                'attr' => [
                    'placeholder' => 'Ex: 999.17',
                    'class' => 'form-control',
                    'step' => '0.01', // l'utilisateur peut taper des centimes
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le prix HT est obligatoire.']),
                    new Positive(['message' => 'Le prix doit être supérieur à 0.']),
                ],
            ])

            // Champ Taux TVA : le taux de TVA sous forme décimale (0.20 = 20%)
            ->add('vat_rate', NumberType::class, [
                'label' => 'Taux de TVA (ex: 0.20 pour 20%)',
                'scale' => 2,
                'attr' => [
                    'placeholder' => 'Ex: 0.20',
                    'class' => 'form-control',
                    'step' => '0.01',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le taux de TVA est obligatoire.']),
                    new Range([
                        'min' => 0,
                        'max' => 1,
                        'notInRangeMessage' => 'La TVA doit être entre 0 (0%) et 1 (100%).',
                    ]),
                ],
            ])

            // Champ Quantité : le stock disponible du produit
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité en stock',
                'attr' => [
                    'placeholder' => 'Ex: 50',
                    'class' => 'form-control',
                    'min' => 0, // on ne peut pas avoir un stock négatif
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La quantité est obligatoire.']),
                    new PositiveOrZero(['message' => 'La quantité ne peut pas être négative.']),
                ],
            ])

            // Champ Catégorie : un menu déroulant avec les catégories disponibles
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'placeholder' => '-- Choisir une catégorie --', // option vide par défaut
                'choices' => [
                    // La clé est ce qui est affiché, la valeur est ce qui est enregistré en BDD
                    'Smartphones' => 'Smartphones',
                    'Ordinateurs'  => 'Ordinateurs',
                    'Tablettes'    => 'Tablettes',
                    'Audio'        => 'Audio',
                    'Consoles'     => 'Consoles',
                    'Caméras'      => 'Caméras',
                    'Drones'       => 'Drones',
                    'Accessoires'  => 'Accessoires',
                ],
                'attr' => ['class' => 'form-select'],
                'required' => false, // la catégorie est nullable dans l'entité donc on la rend optionnelle
            ])

            // Champ Description : un grand champ texte libre
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false, // la description est nullable dans l'entité
                'attr' => [
                    'placeholder' => 'Décrivez le produit en quelques phrases...',
                    'class' => 'form-control',
                    'rows' => 5, // hauteur du textarea
                ],
            ])

            // Champ Image : l'URL de l'image du produit (peut être une URL externe ou un chemin local)
            ->add('image', TextType::class, [
                'label' => 'URL de l\'image',
                'required' => false, // l'image est nullable dans l'entité
                'attr' => [
                    'placeholder' => 'Ex: https://... ou /images/produit.png',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Length(['max' => 255, 'maxMessage' => 'L\'URL ne peut pas dépasser 255 caractères.']),
                ],
            ])
        ;
    }

    // La méthode configureOptions() indique à Symfony que ce formulaire est lié à l'entité Product.
    // Cela permet à Symfony de remplir automatiquement les champs quand on lui passe un objet Product existant.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class, // le formulaire manipule un objet de type Product
        ]);
    }
}
