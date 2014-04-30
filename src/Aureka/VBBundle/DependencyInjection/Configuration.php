<?php

namespace Aureka\VBBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Aureka\VBBundle\VBUser;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('aureka_vb');
        $rootNode
            ->children()
                ->booleanNode('enabled')->defaultValue(true)->end()
                ->scalarNode('license')->isRequired()->end()
                ->scalarNode('default_user_group')->defaultValue(VBUser::DEFAULT_GROUP)->end()
                ->scalarNode('ip_check')->defaultValue(1)->end()
                ->scalarNode('cookie_prefix')->defaultValue('')->end()
                ->arrayNode('database')
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('host')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('port')->isRequired()->end()
                        ->scalarNode('user')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('password')->isRequired()->end()
                        ->scalarNode('table_prefix')->defaultValue('')->end()
                    ->end()
            ->end();
        return $treeBuilder;
    }

}