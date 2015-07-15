<?php

namespace Lpi\NewsletterBundle\Integration\Mailjet\Domain;

use JMS\Serializer\Annotation\Type;

class ContactContainer{

    /**
     * @Type("integer")
     */
    private $Count;

    /**
     * @Type("ArrayCollection<Lpi\NewsletterBundle\Integration\Mailjet\Domain\Contact>")
     */
    private $Data;

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->Count;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->Data;
    }
}
