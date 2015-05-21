<?php

namespace Niif\ShibbolethUserProviderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NiifShibbolethUserProviderExtension extends Extension
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

        if (isset($config['entitlement_prefix'])) {
            $container->setParameter('shib_user_provider.entitlement_prefix',$config['entitlement_prefix']);
        }
        if (isset($config['admin_role_regexp'])) {
            $container->setParameter('shib_user_provider.admin_role_regexp',$config['admin_role_regexp']);
        }
        if (isset($config['user_role_regexp'])) {
            $container->setParameter('shib_user_provider.user_role_regexp',$config['user_role_regexp']);
        }
        if (isset($config['guest_role_regexp'])) {
            $container->setParameter('shib_user_provider.guest_role_regexp',$config['guest_role_regexp']);
        }
        if (isset($config['generate_custom_roles'])) {
            $container->setParameter('shib_user_provider.generate_custom_roles',$config['generate_custom_roles']);
        }
        if (isset($config['entitlement_serverparameter'])) {
            $container->setParameter('shib_user_provider.entitlement_serverparameter',$config['entitlement_serverparameter']);
        }
    }
}
