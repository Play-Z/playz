<?php

namespace App\Controller\Payment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PaypalService;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Date;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'payment')]
    public function index(): Response
    {
        $priceMonth = 15;
        $priceYear = 80;
        return $this->render('payment/index.html.twig',[
            'priceMonth' => $priceMonth,
            'priceYear' => $priceYear
        ]);
    }

    #[Route('/paymentSuccess/{id}', name: 'payment_success')]
    public function checkPayment($id, PaypalService $paypalService, Security $security, UserRepository $userRepository): Response
    {
        $paypalService->verifyOrder($id);
        $user = $userRepository->findByEmail($security->getUser()->getUserIdentifier());
        if (!$paypalService->verifyOrder($id)){
            /* set page erreur paiement */
            $user->addRole('ROLE_SUBSCRIBE');
            $user->setDateSubscribe(new Date());
        }

        /* set time out of 1 month */
        if (new Date() > $user->getDateSubscribe()->modify('+1 month')){
            $user->removeRole('ROLE_SUBSCRIBE');
        }

        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->render('payment/paymentSuccess.html.twig');
    }
}
