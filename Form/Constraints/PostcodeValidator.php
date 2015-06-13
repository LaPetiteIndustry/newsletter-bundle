<?php


namespace Lpi\NewsletterBundle\Form\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PostcodeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!Utils::isValidFrenchPostcode($value)) {

            $this->context->buildViolation($constraint->message)
                ->setParameter('%string%', $value)
                ->addViolation();
        }
    }
}