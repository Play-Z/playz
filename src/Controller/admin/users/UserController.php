<?php

namespace App\Controller\admin\users;

use App\Entity\User;
use App\Form\CreateUserType;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET','POST'])]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new User();
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setIsVerified(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre utilisateur a bien été créer !');

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    // show user profil in POST request
    #[Route('/{slug}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{slug}/edit', name: 'user_edit', methods: ['GET','POST'])]
    public function edit(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasherInterface, MailerInterface $mailer, ResetPasswordHelperInterface $resetPasswordHelper): Response
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = random_bytes(10);
            $user->setPassword($userPasswordHasherInterface->hashPassword($user, $password));
            $user->setIsVerified(true);

            try {
                $resetToken = $resetPasswordHelper->generateResetToken($user);
            } catch (ResetPasswordExceptionInterface $e) {
                $this->addFlash('error', "Erreur lors de l'envoi du mail");
                return $this->redirectToRoute('user_edit', ['slug' => $user->getSlug()], Response::HTTP_SEE_OTHER);
            }

            $email = (new TemplatedEmail())
                ->from(new Address('playZ@gmail.com', 'PlayZ'))
                ->to($user->getEmail())
                ->subject('PlayZ - Votre compte à été modifié par l\'administrateur PlayZ. Veuillez changer votre mot de passe')
                ->htmlTemplate('reset_password/user_update_email.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                ]);

            $mailer->send($email);


            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifier !");

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        } 

        return $this->renderForm('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete_subscribe', name: 'delete_subscribe', methods: ['GET','POST'])]
    public function deleteSubscribe(Request $request, User $user): Response
    {
        /* Suppression du role subscribe */
        $user->removeRole('ROLE_SUBSCRIBE');
        $user->deleteDateSubscribe();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_dashboard', []);
    }

    #[Route('/{slug}', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', "L'utilisateur a bien été supprimer !");
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
