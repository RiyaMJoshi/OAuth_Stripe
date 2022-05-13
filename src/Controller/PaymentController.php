<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(Request $request, $stripeSK): Response
    {
        Stripe::setApiKey($stripeSK);

        $product_name = $request->request->get('product');
        $unit_amount = ($request->request->get('unitPrice')) * 100;
        $quantity = $request->request->get('quantity');

        $session = Session::create([
            'line_items' => [[
              'price_data' => [
                'currency' => 'inr',
                'product_data' => [
                  'name' => $product_name,
                ],
                'unit_amount' => $unit_amount,
              ],
              'quantity' => $quantity,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
          ]);

          // dd($session);
          // return $response->withHeader('Location', $session->url)->withStatus(303);
          return $this->redirect($session->url, 303);
    }

    #[Route('/success-url', name: 'success_url')]
    public function successUrl(): Response
    {
        return $this->render('payment/success.html.twig', []);
    }

    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelUrl(): Response
    {
        return $this->render('payment/cancel.html.twig', []);
    }

}
