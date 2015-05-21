<?php

namespace Niif\ShibbolethUserProviderBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('shibboleth_user_provider');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->scalarNode('entitlement_prefix')->defaultValue("")->end()
                ->scalarNode('admin_role_regexp')->defaultValue("/^admin$/")->end()
                ->scalarNode('user_role_regexp')->defaultValue("/^user$/")->end()
                ->scalarNode('guest_role_regexp')->defaultValue("/^guest$/")->end()
                ->booleanNode('generate_custom_roles')->defaultFalse()->end()
                ->scalarNode('custom_role_prefix')->defaultValue("")->end()
                ->scalarNode('custom_additional_role')->defaultValue("ROLE_USER")->end()
                ->scalarNode('entitlement_serverparameter')->defaultValue("")->end()
            ->end();
        return $treeBuilder;
    }
}
