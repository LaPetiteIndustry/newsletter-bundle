<?php
namespace Lpi\NewsletterBundle\Test;

use GuzzleHttp\Client;
use Lpi\NewsletterBundle\Entity\Customer;
use Lpi\NewsletterBundle\Integration\Access;
use Lpi\NewsletterBundle\Integration\Mailjet;

class MailjetTest extends KernelAwareTest
{

    /*
     curl -X POST --user "bc3e4acf401bdc1079797221c5f384b3:6f712d968b99cd48d4cd3bda71fe2420" https://api.mailjet.com/v3/REST/contactdata -H 'Content-Type: application/json' -d '{"ContactID":78133,"data":[{"Name":"Nom","Value":"Puleri 3"},{"Name":"Pr\u00e9nom","Value":"David 2"},{"Name":"Cp","Value":"06000"}]}'
     */
    public function testOk(){

//        $apiKey = 'bc3e4acf401bdc1079797221c5f384b3';
//        $apiSecret = '6f712d968b99cd48d4cd3bda71fe2420';
//
//        $client = new Client([
//            'base_url' => 'https://api.mailjet.com/v3/REST/',
//            'defaults' => [
//                'auth'    => [$apiKey, $apiSecret]
//            ]
//        ]);


        //pour effectuer le test il faut changer l'email
        $customer = new Customer();
        $customer->setFirstName('David 2');
        $customer->setLastName('Puleri 3');
        $customer->setDepartment('06000');
        $customer->setEmailAddress('davidb305@lapetiteindustry.com');

        $this->em->persist($customer);
        $this->em->flush();
    }
}