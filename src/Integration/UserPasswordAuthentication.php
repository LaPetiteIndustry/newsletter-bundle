<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 29/07/2015
 * Time: 15:51
 */

namespace Lpi\NewsletterBundle\Integration;


class UserPasswordAuthentication
{
    private $user;
    private $password;

    public function __construct($user, $password){

        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }


}