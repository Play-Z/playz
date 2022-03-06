<?php

namespace App\Controller\Payment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'payment')]
    public function index(): Response
    {

        $priceMonth = 15;
        $priceYear = 80;
        return $this->render('payment/index.html.twig',[
            'priceMonth' => $priceMonth,
            'priceYear' => $priceYear,
        ]);
    }
}
