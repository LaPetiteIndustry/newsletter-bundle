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
use Monolog\Logger;

class CustomerListener {
    protected $logger;
    protected $mailjet;

    public function __construct(Logger $logger, Mailjet $mailjet) {
        $this->logger = $logger;
        $this->mailjet = $mailjet;
    }

    public function postPersist(LifecycleEventArgs $args) {
        $entity = $this->checkArgs($args);
        if (!$entity) {
            return;
        }
        $res = $this->mailjet->registerCustomer($entity);
        if (is_array($res)) {
            $res = implode(', ', $res);
        }
        $this->logger->log(Logger::INFO, $res);
    }

    public function postUpdate(LifecycleEventArgs $args) {
        $entity = $this->checkArgs($args);
        if (!$entity) {
            return;
        }
        $res = $this->mailjet->registerCustomer($entity);
        if (is_array($res)) {
            $res = implode(', ', $res);
        }
        $this->logger->log(Logger::INFO, $res);
    }

    protected function checkArgs(LifecycleEventArgs $args) {
        $entity = $args->getObject();

        if ($entity instanceof Customer) {
            return $entity;
        } else {
            return false;
        }
    }
} 