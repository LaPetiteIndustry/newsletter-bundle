<?php

namespace Lpi\NewsletterBundle\Entity;

use Lpi\NewsletterBundle\Model\Customer as BaseCustomer;

class Customer extends BaseCustomer
{
    /**
     * @var integer $id
     */
    protected $id;
    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

}