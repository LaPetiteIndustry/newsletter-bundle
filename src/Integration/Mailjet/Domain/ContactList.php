<?php

namespace Lpi\NewsletterBundle\Integration\Mailjet\Domain;

use JMS\Serializer\Annotation\Type;

class ContactList {


    /**
     * @Type("string")
     */
    private $Name;
    /**
     * @Type("integer")
     */
    private $ID;

    /**
     * @Type("string")
     */
    private $Address;

    /**
     * @Type("boolean")
     */
    private $IsDeleted;

    /**
     * @Type("integer")
     */
    private $SubscriberCount;

    public function __construct($name) {
        $this->Name = $name;
    }


    public function toMailjet(){
        return json_encode(['Name' => $this->Name]);
    }

    public function getName(){
        return $this->Name;
    }

    public function getId(){
        return $this->ID;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->Address;
    }

    /**
     * @param mixed $Address
     */
    public function setAddress($Address)
    {
        $this->Address = $Address;
    }

    /**
     * @param mixed $ID
     */
    public function setID($ID)
    {
        $this->ID = $ID;
    }




}