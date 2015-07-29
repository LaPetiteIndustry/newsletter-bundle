<?php

namespace Lpi\NewsletterBundle\Integration\Mailjet;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Lpi\NewsletterBundle\Integration\ClientIntegration;
use Lpi\NewsletterBundle\Integration\Mailjet\Domain\ListRecipient;
use Lpi\NewsletterBundle\Integration\UserPasswordAuthentication;
use Monolog\Logger;

class MailjetClient extends ClientIntegration
{

    public function __construct(UserPasswordAuthentication $auth, Logger $logger)
    {
        parent::__construct('https://api.mailjet.com/v3/REST/', $auth->getUser(), $auth->getPassword(), $logger);
    }

    public function getContact($email)
    {
        $req = new Request('GET', 'contact/' . $email);
        try {
            $response = $this->send($req);
        } catch (\GuzzleHttp\Exception\ClientException $exception) {

            switch ($exception->getResponse()->getStatusCode()) {
                case 404:
                    throw new ContactNotFoundException('Unable to find contact ' . $email);
                    break;

                default:
                    throw new ContactNotFoundException('Unable to find contact ' . $email);
                    break;
            }

        }

        return $response->getBody()->getContents();
    }


    public function getContactLists()
    {
        $req = new Request('GET', 'contactslist');
        $response = $this->send($req);
        return $response->getBody()->getContents();
    }

    public function registerRecipientToContactList(ListRecipient $listRecipient)
    {
        try {
            $req = new Request('POST', 'listrecipient', ['Content-Type' => 'application/json'], $listRecipient->payload());
            $this->send($req);
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            $this->logger->error($exception->getMessage() . ' - Unable to register ' . $listRecipient->getContactID() . ' to ' . $listRecipient->getListID() . ' Already registered ?');
            throw new UnableToRegisterEmailToContactList(json_encode($listRecipient));
        }
        return true;
    }

    public function updateMetadata($body)
    {
        $this->logger->notice('Trying to create metatdata');
        $req = new Request('POST', 'contactdata', ['Content-Type' => 'application/json'], $body);
        $this->send($req);
        return true;
    }

    public function getListRecipients()
    {
        $req = new Request('GET', 'listrecipient', ['Content-Type' => 'application/json']);
        $response = $this->send($req);
        return $response->getBody()->getContents();
    }

    public function getContactList($getId)
    {

        $req = new Request('GET', 'contactslist/' . $getId);

        try {
            $response = $this->send($req);
        } catch (\GuzzleHttp\Exception\ClientException $exception) {

            switch ($exception->getResponse()->getStatusCode()) {
                case 404:
                    throw new ContactListNotFoundException('Unable to find contact list ' . $getId);
                    break;

                default:
                    throw $exception;
                    break;
            }

        }

        return $response->getBody()->getContents();

    }
}