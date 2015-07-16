<?php
namespace Lpi\NewsletterBundle\Integration\Mailjet;


use GuzzleHttp\Psr7\Request;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use Lpi\NewsletterBundle\Integration\Mailjet\Domain\Contact;
use Lpi\NewsletterBundle\Integration\Mailjet\Domain\ContactList;
use Lpi\NewsletterBundle\Integration\Mailjet\Domain\ContactMetadata;
use Lpi\NewsletterBundle\Integration\Mailjet\Domain\ListRecipient;
use Symfony\Bridge\Monolog\Logger;

class MailjetService
{

    /**
     * @var MailjetClient
     */
    private $client;
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(MailjetClient $client, Logger $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }


    public function createList(ContactList $contactList)
    {
        $req = new Request('POST', 'contactslist', ['Content-Type' => 'application/json'], $contactList->toMailjet());
        try {
            $this->logger->notice("Trying to create contact list named " . $contactList->getName());
            $this->client->send($req);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            return false;
        }


        $this->logger->notice("Contact list created");
        return true;
    }

    /**
     * @param Contact $contact
     * @throws ContactNotFoundException
     * @throws \Exception
     */
    public function createContact(Contact $contact)
    {
        $req = new Request('POST', 'contact', ['Content-Type' => 'application/json'], $contact->toMailjet());
        try {
            $this->logger->notice("Trying to create contact with " . $contact->getEmail());
            $this->client->send($req);
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            $this->logger->notice("Unable to create contact " . $contact->getEmail());
            return false;

        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            return false;
        }

        $this->logger->notice("Contact created : " . $contact->getEmail());
        return true;
    }

    private function getContact($email)
    {
        $data = $this->client->getContact($email);
        $serializer = \JMS\Serializer\SerializerBuilder::create()->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())->build();
        return $serializer->deserialize($data, 'Lpi\NewsletterBundle\Integration\Mailjet\Domain\ContactContainer', 'json')->getData()->first();
    }

    public function registerContactToContactList(Contact $contact, ContactList $list)
    {
        $serializer = \JMS\Serializer\SerializerBuilder::create()->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())->build();



        try {
            $body = $this->client->getContact($contact->getEmail());
            $contact = $serializer->deserialize($body, 'Lpi\NewsletterBundle\Integration\Mailjet\Domain\ContactContainer', 'json')->getData()->first();
            $data = $this->client->getContactList($list->getId());
            $list =  $serializer->deserialize($data, 'Lpi\NewsletterBundle\Integration\Mailjet\Domain\ContactListContainer', 'json')->getData()->first();

            $this->logger->notice("Trying to register contact " . $contact->getEmail() . " to " . $list->getName());
            $listRecipient = new ListRecipient($contact->getId(), $list->getId());

            $this->client->registerRecipientToContactList($listRecipient);

        } catch (ContactListNotFoundException $exception) {
            $this->logger->notice("Unable to register contact " . $contact->getEmail() . " Contact List not found ");
            return false;
        } catch (ContactNotFoundException $exception) {
            $this->logger->notice("Unable to load contact " . $contact->getEmail() . " Contact not found ");
            return false;
        } catch (UnableToRegisterEmailToContactList $exception) {
            $this->logger->notice("Unable to register contact " . $contact->getEmail() . " to " . $list->getName());
            return false;
        }

        $this->logger->notice("Contact " . $contact->getEmail() . " succesfully registered to " . $list->getName());
        return true;
    }

    public function clearContactList(ContactList $list)
    {
        $data = $this->client->getListRecipients();

        $serializer = \JMS\Serializer\SerializerBuilder::create()->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())->build();
        $r = $serializer->deserialize($data, 'Lpi\NewsletterBundle\Integration\Mailjet\Domain\ListRecipientContainer', 'json')->getData();

        $filtered = array_filter($r->toArray(), function ($e) use ($list) {
            return $e->getListName() == $list->getName();
        });

        if (count($filtered) == 0) {
            return false;
        }

        foreach ($filtered as $element) {
            try {
                $this->client->send(new Request('DELETE', 'listrecipient/' . $element->getID(), ['Content-Type' => 'application/json']));
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage() . ' - Unable to clear contact list ');
                return false;
            }
        }

        return true;

    }

    public function updateContact(Contact $lpi, $data)
    {
        try {
            $contact = $this->getContact($lpi->getEmail());

            $newArr = array_map(function ($element) {
                return ['Name' => $element['key']->getName(), 'Value' => $element['value']];
            }, $data);

            $body = json_encode(['ContactID' => $contact->getID(), 'Data' => $newArr]);
            $this->client->updateMetadata($body);

        } catch (ContactNotFoundException $exception) {
            return false;
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            return false;
        }
        return true;
    }

    public function addContactMetadata(ContactMetadata $param)
    {
        $req = new Request('POST', 'contactmetadata', ['Content-Type' => 'application/json'], $param->payload());
        try {
            $this->logger->notice('Trying to create metadata parameter');
            $this->client->send($req);
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            $this->logger->error($exception->getMessage() . ' - Unable to create metadata : Already existing ?');
            return false;
        }
        return true;
    }

}

