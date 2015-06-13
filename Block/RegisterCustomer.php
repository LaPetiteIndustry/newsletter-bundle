<?php
namespace Lpi\NewsletterBundle\Block;

use Lpi\NewsletterBundle\Form\CustomerType;
use Sonata\BlockBundle\Block\BlockServiceInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\Routing\Router;

class RegisterCustomer extends BaseBlockService implements BlockServiceInterface {

    /**
     * @var FormFactory
     */
    private $formFactory;
    /**
     * @var Router
     */
    private $router;

    public function __construct($name, EngineInterface $templating, FormFactory $formFactory, Router $router)
    {
        parent::__construct($name, $templating);
        $this->formFactory = $formFactory;
        $this->router = $router;
    }


    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'LpiNewsletterBundle:Block:register_customer.html.twig',
        ));

        $resolver->setOptional(['hideIdentity']);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $customerType = new CustomerType();


        $form = $this->formFactory->createBuilder($customerType, [], []);
        $form->setAction($this->router->generate("lpi_newsletter_registration_ajax"));

        $settings = $blockContext->getSettings();

        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings,
            'form' => $form->getForm()->createView()
        ), $response);
    }
}