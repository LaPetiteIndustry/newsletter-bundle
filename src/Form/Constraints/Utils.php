<?php
/**
 * Created by IntelliJ IDEA.
 * User: david
 * Date: 16/04/2015
 * Time: 16:49
 */

namespace Lpi\NewsletterBundle\Form\Constraints;


class Utils {
    static public function isValidFrenchPostcode($value){
        return (preg_match('/^[0-9-]{5}+$/', $value, $matches) == 1) ? true : false;
    }
}