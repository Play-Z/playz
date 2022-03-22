<?php

namespace App\Controller\settings;

use App\Entity\User;
use App\Form\ChangeEmailType;
use App\Form\ChangePasswordFormType;
use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/account')]
class UserAccountController extends AbstractController
{
    #[Route('/', name: 'user_account', methods: ['GET','POST'])]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $emailForm = $this->createForm(ChangeEmailType::class, $user);
        $passwordForm = $this->createForm(ChangePasswordFormType::class, $user);

        if ($request->request->has('change_password_form')){
            $passwordForm->handleRequest($request);
            if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
                $encodedPassword = $userPasswordHasherInterface->hashPassword(
                    $user,
                    $passwordForm->get('plainPassword')->getData()
                );

                $user->setPassword($encodedPassword);
                $this->getDoctrine()->getManager()->flush();
            }
        }
        else{
            $emailForm->handleRequest($request);
            if ($emailForm->isSubmitted() && $emailForm->isValid()) {

                if ($emailForm->get('password')->getData() == $user->getPassword()){
                    $user->setEmail($emailForm->get('email')->getData());
                    $this->getDoctrine()->getManager()->flush();
                }
            }
        }

        return $this->render('settings/account/account.html.twig', [
            'user' => $user->getSlug(),
            'emailForm' => $emailForm->createView(),
            'passwordForm' => $passwordForm->createView()
        ]);
    }

    #[Route('/close', name: 'user_account_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_account_index', [], Response::HTTP_SEE_OTHER);
    }
}
