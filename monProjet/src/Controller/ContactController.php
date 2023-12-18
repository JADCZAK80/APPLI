<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\DemoFormType;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //on crée une instance de Contact
            $message = new Contact();
            // Traitement des données du formulaire
            $data = $form->getData();
            //on stocke les données récupérées dans la variable $message
            $message = $data;

            $entityManager->persist($message);
            $entityManager->flush();
            
                if ($form->isSubmitted() && $form->isValid()) {
                    // Récupérer les données du formulaire
                    $data = $form->getData();
            
                    // Envoyer un email avec TemplatedEmail
                    $email = (new TemplatedEmail())
                        ->from('your_email@example.com')
                        ->to('recipient@example.com')
                        ->subject('New Contact Form Submission')
                        ->htmlTemplate('emails/contact_email.html.twig') // Créez ce template Twig dans templates/emails/
                        ->context([
                            'objet' => $message->getObjet(),
                            'user_email' => $message->getEmail(),
                            'message' => $message->getMessage(),
                        ]);
            
                    $mailer->send($email);
            
                    $this->addFlash('success', 'Your message has been sent successfully.');
                
            }
            
            // Redirection vers accueil
            return $this->redirectToRoute('app_contact_success', [
                'user_email' => $message->getEmail(), // Ajout de 'user_email' dans les paramètres de la redirection
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