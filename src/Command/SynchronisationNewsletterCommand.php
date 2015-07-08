<?php
/**
 * Created by PhpStorm.
 * User: jeremy
 * Date: 21/04/15
 * Time: 11:31
 */

namespace Lpi\NewsletterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronisationNewsletterCommand extends ContainerAwareCommand {
    protected function configure() {
        $this
            ->setName('lpi:newsletter:sync')
            ->setDescription("Commande de synchronisation des newsletter.");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln(sprintf('<info>%s</info>', "Recherche de toutes les newsletter..."));
        $_em = $this
            ->getContainer()
            ->get('doctrine.orm.default_entity_manager');
        $newsletters = $_em
            ->getRepository('LpiNewsletterBundle:Customer')
            ->findAll();

        $output->writeln(sprintf('<info>%s newsletter trouvées</info>', count($newsletters)));
        if (count($newsletters) > 0) {
            foreach ($newsletters as $newsletter) {
                $this->getContainer()->get('lpi.mailjet')->updateCustomer($newsletter);
            }
        }

        $output->writeln(sprintf('<info>Toutes les newsletter ont été mises à jour.</info>'));
    }
} 