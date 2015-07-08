<?php
namespace Lpi\NewsletterBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;

class Postcode  extends Constraint  {

    public $message = 'Ce code postal est non valide';

}