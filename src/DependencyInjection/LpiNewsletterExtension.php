<?php

namespace Lpi\NewsletterBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LpiNewsletterExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('admin.xml');

        if (isset($config['mailjet']) && isset($config['mailjet']['list'])) {
            $container->setParameter('mailjet_list', $config['mailjet']['list']);
        }
        if (isset($config['mailjet']) && isset($config['mailjet']['api_key'])) {
            $container->setParameter('mailjet_key', $config['mailjet']['api_key']);
        }
        if (isset($config['mailjet']) && isset($config['mailjet']['api_secret'])) {
            $container->setParameter('mailjet_secret', $config['mailjet']['api_secret']);
        }
        if (isset($config['mailjet']) && isset($config['mailjet']['list_id'])) {
            $container->setParameter('list_id', $config['mailjet']['list_id']);
        }
    }
}
