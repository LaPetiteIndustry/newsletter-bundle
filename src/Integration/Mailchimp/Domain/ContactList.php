<?php

namespace Lpi\NewsletterBundle\Integration\Mailchimp\Domain;

class ContactList
{
    private $listId;

    public function __construct($listId)
    {
        $this->listId = $listId;
    }

    /**
     * @return mixed
     */
    public function getListId()
    {
        return $this->listId;
    }
}