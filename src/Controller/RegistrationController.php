<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User(); //On crée un nouvel objet User
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request); //Symfony récupère les informations envoyées par l'utilisateur.

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            //On transforme le mot de passe en hash avant de le stocker.
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            //on enregistre dans MySQL
            $entityManager->persist($user); //on prépare l'enregistrement.
            $entityManager->flush(); //onexécute réellement l'enregistrement.

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
