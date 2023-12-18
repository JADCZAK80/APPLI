<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class MailService
{
    private $mailer;
    private $paramBag;

    public function __construct(MailerInterface $mailer, ParameterBagInterface $paramBag)
    {
        $this->mailer = $mailer;
        $this->paramBag = $paramBag;

    }

    public function sendMail($expediteur, $destinataire, $sujet, $message)
    {
        // Envoyer un email avec TemplatedEmail
        $email = (new TemplatedEmail())
            ->from($expediteur)
            ->to($destinataire)
            ->subject($sujet)
            ->htmlTemplate('emails/contact_email.html.twig') // Assurez-vous d'avoir ce template Twig dans templates/emails/
            ->context([
                'objet' => $message->getObjet(),
                'user_email' => $message->getEmail(),
                'message' => $message->getMessage(),
            ]);

        $this->mailer->send($email);

    }
}
