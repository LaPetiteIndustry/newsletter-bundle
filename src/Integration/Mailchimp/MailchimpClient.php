<?php
namespace Lpi\NewsletterBundle\Integration\Mailchimp;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use Lpi\NewsletterBundle\Integration\ClientIntegration;
use Lpi\NewsletterBundle\Integration\Mailchimp\Domain\Contact;
use Lpi\NewsletterBundle\Integration\Mailchimp\Domain\ContactList;
use Lpi\NewsletterBundle\Integration\Mailjet\UnableToRegisterEmailToContactList;
use Lpi\NewsletterBundle\Integration\UserPasswordAuthentication;
use Monolog\Logger;

class MailchimpClient extends ClientIntegration
{

    public function __construct(UserPasswordAuthentication $user, Logger $logger)
    {
        parent::__construct('https://us11.api.mailchimp.com/3.0/', $user->getUser(), $user->getPassword(), $logger);
    }

    public function addContactToList(Contact $contact, ContactList $contactList)
    {
        $req = new Request('POST', 'lists/' . $contactList->getListId() . '/members', ['Content-Type' => 'application/json'], $contact->payload());


        try {
            $response = $this->send($req);
        } catch (\GuzzleHttp\Exception\ClientException $exception) {

            $this->getLogger()->error($exception->getResponse()->getBody()->getContents());
            throw new UnableToRegisterEmailToContactList($exception->getResponse()->getBody()->getContents());
        }
    }

}