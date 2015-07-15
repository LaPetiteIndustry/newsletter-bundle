<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 20/04/15
 * Time: 10:51
 */

namespace Lpi\NewsletterBundle\Listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Lpi\NewsletterBundle\Entity\Customer;
use Lpi\NewsletterBundle\Integration\Mailjet;
use Lpi\NewsletterBundle\Integration\Mailjet\Domain\ContactMetadata;
use Monolog\Logger;

class CustomerListener
{
    protected $logger;
    protected $mailjet;
    protected $contactList;

    public function __construct(Mailjet\MailjetService $mailjet, $listName, $listId)
    {
        $this->mailjet = $mailjet;
        $this->contactList = new Mailjet\Domain\ContactList($listName);
        $this->contactList->setID($listId);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $this->checkArgs($args);

        if ($entity instanceof Customer) {

            $contact = new Mailjet\Domain\Contact($entity->getEmailAddress());
            $this->mailjet->createContact($contact);
            $this->mailjet->registerContactToContactList($contact, $this->contactList);

            $metatadas = [
                ['key' => ContactMetadata::createStringMetadata("cp"), 'value' => $entity->getDepartment()],
                ['key' => ContactMetadata::createStringMetadata("nom"), 'value' => $entity->getLastName()],
                ['key' => ContactMetadata::createStringMetadata("prénom"), 'value' => $entity->getFirstName()]
            ];
            
            $this->mailjet->updateContact($contact, $metatadas);
        }

    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $this->checkArgs($args);

        if ($entity instanceof Customer) {

            $contact = new Mailjet\Domain\Contact($entity->getEmailAddress());

            $metatadas = [
                ['key' => ContactMetadata::createStringMetadata("cp"), 'value' => $entity->getDepartment()],
                ['key' => ContactMetadata::createStringMetadata("nom"), 'value' => $entity->getLastName()],
                ['key' => ContactMetadata::createStringMetadata("prénom"), 'value' => $entity->getFirstName()]
            ];

            $this->mailjet->updateContact($contact, $metatadas);
        }
    }

    protected function checkArgs(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Customer) {
            return $entity;
        } else {
            return false;
        }
    }
} 