<?php

namespace Lpi\NewsletterBundle\Integration;

class UserAuthenticationFactory
{
    static public function createUserPasswordAuthentication($user, $password)
    {
        return new UserPasswordAuthentication($user, $password);
    }
}