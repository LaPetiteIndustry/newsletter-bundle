<?php

namespace Lpi\NewsletterBundle\Controller;

use Lpi\NewsletterBundle\Entity\Customer;
use Lpi\NewsletterBundle\Form\CustomerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {

        $customerType = new CustomerType();
        $customer = new Customer();
        $form = $this->createForm($customerType, $customer);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            return new Response('Merci pour votre engagement', 201, ['Content-Type' => 'plain/text; charset=utf-8']);
        }
        $errorMsg = '';
        foreach ($form->getErrors(true, true) as $errors) {
            $errorMsg .= $errors->getMessage().' - ';
        }
        return new Response($errorMsg, 400, ['Content-Type' => 'plain/text; charset=utf-8']);
    }


}
