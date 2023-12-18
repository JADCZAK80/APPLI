<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    private $mailService;

    // Injectez le service MailService dans le constructeur
    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $message = new Contact();
            $data = $form->getData();
            //on stocke les données récupérées dans la variable $message
            $message = $data;

            $entityManager->persist($message);
            $entityManager->flush();

            // Utilisez le service MailService pour envoyer l'e-mail
            $this->mailService->sendMail($message->getEmail(), 'recipient@example.com', 'New Contact Form Submission', $message);
            $this->addFlash('success', 'Your message has been sent successfully.');
            // Redirection vers page success
            return $this->redirectToRoute('app_contact_success', [
                'user_email' => $message->getEmail(),
                'message' => $message->getMessage(),
                'objet' => $message->getObjet(),
            ]);
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/contact/success', name: 'app_contact_success')]
    public function success(Request $request): Response
    {
        // Récupérer la variable 'user_email' de la requête
        $userEmail = $request->query->get('user_email');
        $objet = $request->query->get('objet');
        $message = $request->query->get('message');

        // Vous pouvez ajouter du contenu à afficher sur la page de succès si nécessaire.
        return $this->render('contact/success.html.twig', [
            'user_email' => $userEmail,
            'objet' => $objet,
            'message' => $message,

        ]);
    }
}