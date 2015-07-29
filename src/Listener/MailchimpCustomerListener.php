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
use Lpi\NewsletterBundle\Integration\Mailchimp\Domain\Contact;
use Lpi\NewsletterBundle\Integration\Mailchimp\Domain\ContactList;
use Lpi\NewsletterBundle\Integration\Mailchimp\MailchimpClient;
use Lpi\NewsletterBundle\Integration\Mailjet;
use Lpi\NewsletterBundle\Integration\Mailjet\Domain\ContactMetadata;
use Monolog\Logger;

class MailchimpCustomerListener
{
    protected $logger;
    protected $mailjet;
    protected $contactList;

    public function __construct(MailchimpClient $mailjet, $listId)
    {
        $this->mailjet = $mailjet;
        $this->contactList = new ContactList($listId);
    }

    public function postPersist(LifecycleEventArgs $args)
    {

        $entity = $this->checkArgs($args);

        if ($entity instanceof Customer) {
;
            try {
                $this->mailjet->addContactToList(new Contact($entity->getEmailAddress()), $this->contactList);
            } catch (\Exception $e) {

            }
        }

    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        return null;
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