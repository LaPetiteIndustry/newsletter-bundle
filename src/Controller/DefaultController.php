<?php

namespace Lpi\NewsletterBundle\Controller;

use Doctrine\ORM\EntityManager;
use Lpi\NewsletterBundle\Entity\Customer;
use Lpi\NewsletterBundle\Form\CustomerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Symfony\Component\Templating\EngineInterface;

class DefaultController
{
    /**
     * @var EngineInterface
     */
    private $templating;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(FormFactory $formFactory, Router $router, EntityManager $entityManager)
    {
        $this->formFactory = $formFactory;
        $this->action = $router->generate('lpi_newsletter_registration_ajax');
        $this->form = CustomerType::createForm($formFactory, new Customer(), $this->action);

        $this->entityManager = $entityManager;
    }

    public function getFormView()
    {
        return $this->form->getForm()->createView();
    }

    public function submitFormAction(Request $request)
    {

        $customer = new Customer();
        $form = CustomerType::createForm($this->formFactory, $customer, $this->action)->getForm();;

        $form->handleRequest($request);

        if ($form->isValid()) {

            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            return new Response('Merci pour votre engagement', 201, ['Content-Type' => 'plain/text; charset=utf-8']);
        }
        $errorMsg = '';
        foreach ($form->getErrors(true, true) as $errors) {
            $errorMsg .= $errors->getMessage().' - ';
        }
        return new Response($errorMsg, 400, ['Content-Type' => 'plain/text; charset=utf-8']);
    }


}
