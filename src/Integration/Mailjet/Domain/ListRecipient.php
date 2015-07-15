<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 11/07/2015
 * Time: 21:28
 */

namespace Lpi\NewsletterBundle\Integration\Mailjet\Domain;


use JMS\Serializer\Annotation\Type;

class ListRecipient
{


    /**
     * @Type("integer")
     */
    private $ContactID;

    /**
     * @Type("integer")
     */
    private $ID;

    /**
     * @Type("boolean")
     */
    private $IsActive;

    /**
     * @Type("boolean")
     */
    private $IsUnsubscribed;

    /**
     * @Type("integer")
     */
    private $ListID;

    /**
     * @Type("string")
     */
    private $ListName;

    /**
     * ListRecipient constructor.
     * @param $ContactID
     * @param $ListID
     */
    public function __construct($ContactID, $ListID)
    {
        $this->ContactID = $ContactID;
        $this->ListID = $ListID;
    }

    /**
     * @return mixed
     */
    public function getContactID()
    {
        return $this->ContactID;
    }

    /**
     * @return mixed
     */
    public function getID()
    {
        return $this->ID;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->IsActive;
    }

    /**
     * @return mixed
     */
    public function getIsUnsubscribed()
    {
        return $this->IsUnsubscribed;
    }

    /**
     * @return mixed
     */
    public function getListID()
    {
        return $this->ListID;
    }

    /**
     * @return mixed
     */
    public function getListName()
    {
        return $this->ListName;
    }

    public function payload()
    {
        return json_encode(
            ['ContactID' => $this->getContactID(), 'ListID' => $this->getListID()]
        );
    }


}