<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Salon;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MessageController extends AbstractController
{
    #[Route('/salon/{id}/messages', name: 'app_message')]
    public function index(
        Salon $salon,
        Request $request,
        EntityManagerInterface $entityManager,
        MessageRepository $messageRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setAuteur($this->getUser()); //Associe le message à l'utilisateur connecté
            $message->setSalon($salon);// ssocie le message au salon sélectionné.
            $message->setDateEnvoi(new \DateTimeImmutable());//Enregistre la date et l'heure d'envoi.

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_message', [
                'id' => $salon->getId()
            ]);
        }

        $messages = $messageRepository->findBy(
            ['salon' => $salon],
            ['dateEnvoi' => 'ASC']
        );

        return $this->render('message/index.html.twig', [
            'salon' => $salon,
            'messages' => $messages,
            'form' => $form,
        ]);
    }
}
