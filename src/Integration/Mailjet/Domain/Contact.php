<?php

namespace Lpi\NewsletterBundle\Integration\Mailjet\Domain;


use JMS\Serializer\Annotation\Type;

class Contact
{

    /**
     * @Type("string")
     */
    private $Email;
    /**
     * @Type("integer")
     */
    private $ID;

    public function __construct($email)
    {
        $this->Email = $email;
    }

    public function getEmail()
    {
        return $this->Email;
    }

    public function getId(){
        return $this->ID;
    }

    public function toMailjet()
    {
        return json_encode(['Email' => $this->Email]);
    }
}