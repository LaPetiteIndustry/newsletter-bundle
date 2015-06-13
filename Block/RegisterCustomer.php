<?php
namespace Lpi\NewsletterBundle\Block;

use Lpi\NewsletterBundle\Controller\DefaultController;
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
    /**
     * @var DefaultController
     */
    private $service;

    public function __construct($name, EngineInterface $templating, FormFactory $formFactory, Router $router, DefaultController $service)
    {
        parent::__construct($name, $templating);
        $this->service = $service;
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        return $this->renderResponse('LpiNewsletterBundle:Block:register_customer.html.twig', array(
            'form' => $this->service->getFormView()
        ), $response);
    }


}