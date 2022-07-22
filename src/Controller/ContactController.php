<?php

namespace App\Controller;

use App\Entity\AccountRequest;
use App\Entity\ContactRequest;
use App\Form\AccountRequestType;
use App\Form\ContactType;
use App\Repository\AccountRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contact')]
class ContactController extends AbstractController
{
    #[Route('/', name: 'contact_index', methods: ['GET', 'POST'])]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $contact = new ContactRequest();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();

            $message = (new Email())
                ->from($contactFormData->getEmail())
                ->to('contact@playz.com')
                ->subject($contactFormData->getSubject())
                ->text($contactFormData->getMessage(), 'text/plain');
            $mailer->send($message);

            $this->addFlash('success', 'Votre message a bien été envoyer !');

            return $this->redirectToRoute('contact_index');
        }
        if($form->isSubmitted() && !$form->isValid()) {
            $errors = array();

            foreach ($form->getErrorSchema() as $key => $err) {  
                if ($key) {  
                    $errors[$key] = $err->getMessage();  
                }  
            }
            $this->addFlash('error', 'Votre message n\'a pas été envoyé ! Nous vous contacterons dans les plus brefs délais.');
            
        }

        return $this->renderForm('home/contact/index.html.twig', [
            'form' => $form,
            'errors' => $errors ?? null,
        ]);
    }
}
