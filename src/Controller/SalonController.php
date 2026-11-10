<?php

namespace App\Controller;

use App\Entity\Salon;
use App\Form\SalonType;
use App\Repository\SalonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SalonController extends AbstractController
{
    #[Route('/salon', name: 'app_salon')]
    public function index(SalonRepository $salonRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $salons = $salonRepository->findAll();

        return $this->render('salon/index.html.twig', [
            'salons' => $salons,
        ]);
    }

    #[Route('/salon/nouveau', name: 'app_salon_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // seuls les utilisateurs connectés pourront créer des salons
        $salon = new Salon();

        $form = $this->createForm(SalonType::class, $salon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Enregistre le salon dans la base de donnée
            $entityManager->persist($salon);
            $entityManager->flush();

            return $this->redirectToRoute('app_salon');
        }

        return $this->render('salon/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/salon/{id}/modifier', name: 'app_salon_edit')]
    public function edit(
        Salon $salon, //récupère le salon à modifier grâce à son identifiant
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(SalonType::class, $salon);//préremplit le formulaire avec les informations existantes
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); //enregistre les modifications dans MySQL

            return $this->redirectToRoute('app_salon');
        }

        return $this->render('salon/edit.html.twig', [
            'form' => $form,
            'salon' => $salon,
        ]);
    }

    #[Route('/salon/{id}/supprimer', name: 'app_salon_delete', methods: ['POST'])]
    public function delete(
        Salon $salon,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid(
            'delete'.$salon->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException();
        }

        if (!$salon->getMessages()->isEmpty()) {
            $this->addFlash('error', 'Impossible de supprimer un salon qui contient des messages.');
        } else {
            $entityManager->remove($salon);
            $entityManager->flush();

            $this->addFlash('success', 'Le salon a été supprimé.');
        }

        return $this->redirectToRoute('app_salon');
    }

}