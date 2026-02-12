<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\{TextType, EmailType, PasswordType, SubmitType};
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormError;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response
    {
        $user = new Utilisateurs();
        
        // Définir la date d'inscription par défaut
        $user->setDateinscription(new \DateTime());
        // Définir un rôle par défaut
        $user->setRole('ROLE_USER');

        // Création du formulaire SANS contraintes sur le mot de passe
        $form = $this->createFormBuilder($user)
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom ne peut pas être vide']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-]+$/',
                        'message' => 'Le nom ne doit contenir que des lettres, espaces et tirets'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom ne peut pas être vide']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\s\-]+$/',
                        'message' => 'Le prénom ne doit contenir que des lettres, espaces et tirets'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email ne peut pas être vide']),
                    new Assert\Email(['message' => 'L\'email "{{ value }}" n\'est pas une adresse email valide']),
                    new Assert\Length([
                        'max' => 180,
                        'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('motdepasse', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                // AUCUNE contrainte de validation pour le mot de passe
            ])
            ->add('register', SubmitType::class, ['label' => 'S’inscrire'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Vérifier l'unicité de l'email
            $email = $form->get('email')->getData();
            
            if (!empty($email)) {
                $existingUser = $em->getRepository(Utilisateurs::class)
                    ->findOneBy(['email' => $email]);
                
                if ($existingUser !== null) {
                    $form->get('email')->addError(new FormError(
                        'Cette adresse email est déjà utilisée. Veuillez en choisir une autre.'
                    ));
                }
            }

            // Vérification simple que le mot de passe n'est pas vide
            $plainPassword = $form->get('motdepasse')->getData();
            if (empty($plainPassword)) {
                $form->get('motdepasse')->addError(new FormError(
                    'Le mot de passe ne peut pas être vide'
                ));
            }

            // Validation du formulaire
            if ($form->isValid()) {
                // Hasher le mot de passe
                $user->setMotdepasse(
                    $hasher->hashPassword($user, $plainPassword)
                );

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}