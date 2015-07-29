<?php
namespace Lpi\NewsletterBundle\Integration\Mailchimp;

use Lpi\NewsletterBundle\Integration\Mailchimp\Domain\Contact;
use Lpi\NewsletterBundle\Integration\Mailchimp\Domain\ContactList;
use Lpi\NewsletterBundle\Integration\UserAuthenticationFactory;
use Monolog\Logger;

class MailchimpServiceTest extends \PHPUnit_Framework_TestCase {

    public function testMailchimp(){
        $logger = new Logger('mailchimp');

        $userPasswordAuthentication = UserAuthenticationFactory::createUserPasswordAuthentication("", "");
        $mailchimpClient = new MailchimpClient($userPasswordAuthentication, $logger );
        $contactList = new ContactList('');
        $contact = new Contact("");
        $ls = $mailchimpClient->addContactToList($contact, $contactList);
    }

}