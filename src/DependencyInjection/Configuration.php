<?php

namespace Lpi\NewsletterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lpi_newsletter');

        $rootNode
            ->children()
                ->arrayNode('mailjet')
                    ->children()
                        ->scalarNode('list')->cannotBeEmpty()->end()
                        ->scalarNode('api_key')->cannotBeEmpty()->end()
                        ->scalarNode('api_secret')->cannotBeEmpty()->end()
                        ->scalarNode('list_id')->defaultValue(null)->end()
                    ->end()
                ->end()
            ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
