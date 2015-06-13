<?php

namespace Lpi\NewsletterBundle\Model;

interface CustomerInterface
{
    public function getFirstName();

    public function getLastName();

    public function getEmailAddress();

    public function getDepartment();

    public function setFirstName($firstName);

    public function setLastName($lastName);

    public function setEmailAddress($emailAdress);

    public function setDepartment($department);

}