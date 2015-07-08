<?php

namespace Lpi\NewsletterBundle\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Event\EndEvent;
use GuzzleHttp\Exception\ClientException;
use Lpi\NewsletterBundle\Model\Customer;
use Symfony\Bridge\Monolog\Logger;

/**
 * Class Mailjet
 * @package Lpi\NewsletterBundle\Integration
 */
class Mailjet
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $listId;

    /**
     * @param Client $client
     * @param Logger $logger
     * @param string $listId
     */
    public function __construct(Client $client, Logger $logger, $listId)
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->listId = $listId;
    }

    /**
     * Will call mailjet API and create a contact
     * then call the createMetaData for customer entity
     * then append the customer to the defined list
     *
     * @param Customer $customer
     */
    public function registerCustomer(Customer $customer)
    {
        return;
        $params = ['body' => ['Email' => $customer->getEmailAddress()]];
        $req = $this->client->createRequest('POST', 'contact', $params);

        $this->logger->log(Logger::INFO, $req->__toString());

        try {
            $req->getEmitter()->on('end', function(EndEvent $e) use ($customer){
                if ($e->getException()) {
                    $this->updateCustomer($customer);
                } else {
                    $response = json_decode($e->getResponse()->getBody()->read(1024));
                    if (null !== $response->Data[0]) {
                        $userId = $response->Data[0]->ID;
                        $this->logger->log(Logger::INFO, sprintf('[PERSIST] - Found ID (%s) for customer %s', $userId, $customer->getEmailAddress()));
                        if ($userId) {
                            $this->promiseCreateMetaData($userId, $customer);
                        }
                    }
                }
            });

            $resp = $this->client->send($req);


            $this->logger->log(Logger::INFO, sprintf('[PERSIST] - Customer %s has been sent to mailjet', $customer->getEmailAddress()));

            return array(
                'status' => $resp->getStatusCode(),
                'message' => $resp->getReasonPhrase()
            );
        } catch (ClientException $ce) {
            $this->logger->log(Logger::ERROR, '[PERSIST] [ClientException] - '.$ce->getRequest()->__toString());
            $this->logger->log(Logger::ERROR, '[PERSIST] [ClientException] - '.$ce->getResponse()->__toString());
        } catch (\Exception $e) {
            $this->logger->log(Logger::ERROR, '[PERSIST] [Exception] - '.$e->getRequest()->__toString());
            $this->logger->log(Logger::ERROR, '[PERSIST] [Exception] - '.$e->getResponse()->__toString());
        }
    }

    /**
     * Will search the contact in mailjet with email property
     * then update contact metaData
     * then appeld the customer to the defined list
     *
     * @param Customer $customer
     */
    public function updateCustomer(Customer $customer)
    {
        return;
        try {
            $this->logger->log(Logger::INFO, sprintf('[UPDATE] - Get ID for customer %s', $customer->getEmailAddress()));
            $req = $this->client->createRequest('GET', 'contact/'.$customer->getEmailAddress(), []);
            $this->logger->log(Logger::INFO, $req->__toString());

            $req->getEmitter()->on('end', function(EndEvent $e) use ($customer){
                if ($e->getException()) {
                    throw $e->getException();
                } else {
                    $response = json_decode($e->getResponse()->getBody()->read(1024));
                    if (null !== $response->Data[0]) {
                        $userId = $response->Data[0]->ID;
                        $this->logger->log(Logger::INFO, sprintf('[UPDATE] - Found ID (%s) for customer %s', $userId, $customer->getEmailAddress()));
                        if ($userId) {
                            $this->promiseUpdateMetaData($userId, $customer);
                        }
                    }
                }
            });

            $resp = $this->client->send($req);

            $this->logger->log(Logger::INFO, sprintf('[UPDATE] - Customer %s has been updated', $customer->getEmailAddress()));

            return array(
                'status' => $resp->getStatusCode(),
                'message' => $resp->getReasonPhrase()
            );
        } catch (ClientException $ce) {
            $this->logger->log(Logger::ERROR, '[UPDATE] [ClientException] - '.$ce->getRequest()->__toString());
            $this->logger->log(Logger::ERROR, '[UPDATE] [ClientException] - '.$ce->getResponse()->__toString());
        } catch (\Exception $e) {
            $this->logger->log(Logger::ERROR, '[UPDATE] [Exception] - '.$e->getRequest()->__toString());
            $this->logger->log(Logger::ERROR, '[UPDATE] [Exception] - '.$e->getResponse()->__toString());
        }
    }

    /**
     * call the API for creating contact metaData
     *
     * @param string   $userId
     * @param Customer $customer
     */
    private final function promiseCreateMetaData($userId, Customer $customer) {
        $this->logger->log(Logger::INFO, sprintf('[PERSIST] - Will try to create metaData for customer %s', $userId));
        $dataRequest = $this->callContactData('POST', $userId, $customer);

        try {
            $this->client->send($dataRequest);

            $this->logger->log(Logger::INFO, sprintf('[UPDATE] - MetaData for customer %s has been updated', $userId));
        } catch (ClientException $ce) {
            $this->logger->log(Logger::ERROR, '[PERSIST] [ClientException] - '.$ce->getRequest()->__toString());
            $this->logger->log(Logger::ERROR, '[PERSIST] [ClientException] - '.$ce->getResponse()->__toString());
            $this->logger->log(Logger::ERROR, sprintf('[UPDATE] [ClientException] - Cannot update metadata for customer %s : %s',$customer->getEmailAddress(), $ce->getMessage()));
        }
    }

    /**
     * call the API for updating contact metaData
     *
     * @param string   $userId
     * @param Customer $customer
     */
    private final function promiseUpdateMetaData($userId, Customer $customer) {
        $this->logger->log(Logger::INFO, sprintf('[UPDATE] - Will try to update metaData for customer %s', $userId));
        $dataRequest = $this->callContactData('PUT', $userId, $customer);

        try {
            $this->client->send($dataRequest);

            $this->logger->log(Logger::INFO, sprintf('[UPDATE] - MetaData for customer %s has been updated', $userId));
        } catch (ClientException $ce) {
            $this->logger->log(Logger::ERROR, '[PERSIST] [ClientException] - '.$ce->getRequest()->__toString());
            $this->logger->log(Logger::ERROR, '[PERSIST] [ClientException] - '.$ce->getResponse()->__toString());
            $this->logger->log(Logger::ERROR, sprintf('[UPDATE] [ClientException] - Cannot update metadata for customer %s : %s',$customer->getEmailAddress(), $ce->getMessage()));
        }
    }

    /**
     * Wrapper for the metaData calls
     *
     * @param string   $type the type of the request POST|PUT
     * @param string   $userId
     * @param Customer $customer
     *
     * @return \GuzzleHttp\Message\Request|\GuzzleHttp\Message\RequestInterface
     */
    private function callContactData($type, $userId, Customer $customer)
    {
        $dataRequest = $this->client->createRequest($type, 'contactdata', array(
            'body' => json_encode(array(
                'ContactID' => $userId,
                'data' => $customer->__toMailJetFormat()
            ))
        ));
        $this->logger->log(Logger::INFO, $dataRequest->__toString());
        $this->logger->log(Logger::INFO, $dataRequest->getBody()->getContents());
        $dataRequest->getEmitter()->on('end', function(EndEvent $end) use ($userId){
            $this->promiseAppendToList($userId, $this->listId);
        });

        return $dataRequest;
    }

    /**
     * Will append a customer to a list
     *
     * @param string $userId
     * @param string $listId
     */
    private final function promiseAppendToList($userId, $listId) {
        $this->logger->log(Logger::INFO, sprintf('[UPDATE] - Will try to append customer %s to list %s', $userId, $listId));
        $subRequest = $this->client->createRequest('POST', 'listrecipient', array(
            'body' => array(
                'ContactID' => $userId,
                'ListID' => $listId
            )
        ));
        $this->logger->log(Logger::INFO, $subRequest->__toString());
        try {

            $this->client->send($subRequest);
            $this->logger->log(Logger::INFO, sprintf('[PERSIST] - Customer %s has added sent to list %s', $userId, $listId));
        } catch (ClientException $ce) {
            $this->logger->log(Logger::ERROR, '[PERSIST] [ClientException] - '.$ce->getRequest()->__toString());
            $this->logger->log(Logger::ERROR, '[PERSIST] [ClientException] - '.$ce->getResponse()->__toString());
            $this->logger->log(Logger::ERROR, sprintf('[UPDATE] [ClientException] - Customer %s cannot be added to list %s : %s',$userId, $listId, $ce->getMessage()));
        }
    }
}