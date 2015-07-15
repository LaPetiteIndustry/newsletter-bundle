<?php

use Lpi\NewsletterBundle\Form\Constraints\Utils;

class UtilsTest extends \PHPUnit_Framework_TestCase
{

    public function testValidPostCode()
    {
        $this->assertTrue(Utils::isValidFrenchPostcode("32112"));
    }

    public function testInValidPostCode()
    {
        $this->assertFalse(Utils::isValidFrenchPostcode("2000"));
        $this->assertFalse(Utils::isValidFrenchPostcode("242000"));
        $this->assertFalse(Utils::isValidFrenchPostcode("w222d"));
    }
}