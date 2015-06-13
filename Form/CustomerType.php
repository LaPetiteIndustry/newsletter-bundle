<?php
namespace Lpi\NewsletterBundle\Form;

use Lpi\NewsletterBundle\Form\Constraints\Postcode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;


class CustomerType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('lastName', 'text', array('attr' => array('placeholder' => 'Nom'), 'label' => false, 'constraints' => [new NotBlank()]))
            ->add('firstName', 'text', array('attr' => array('placeholder' => 'Prénom'), 'label' => false, 'constraints' => [new NotBlank()]))
            ->add('department', 'text', array('attr' => array('placeholder' => 'Code Postal'), 'label' => false, 'constraints' => [new Postcode()]))
            ->add('emailAddress', 'email', array('attr' => array('placeholder' => 'Email'), 'label' => false, 'constraints' => [new Email()]));
    }

    public function getName()
    {
        return 'lpi_newsletter_registration';
    }
}