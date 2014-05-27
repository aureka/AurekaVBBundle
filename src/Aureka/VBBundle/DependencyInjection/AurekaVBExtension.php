<?php

namespace Aureka\VBBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Reference;

use Aureka\VBBundle\VBConfiguration;

class AurekaVBExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if ($config['enabled']) {
            $this->createConfigurationDefinition($container, $config);
            $this->createLoginListenerDefinition($container, $config);
            $this->createLogoutHandlerDefinition($container, $config);
        }
    }


    private function createConfigurationDefinition(ContainerBuilder $container, array $config)
    {
        $definition = new Definition('Aureka\VBBundle\VBConfiguration');
        $definition->addArgument($config['license']);
        $definition->addArgument($config['default_user_group']);
        $definition->addArgument($config['cookie_prefix']);
        $definition->addArgument($config['ip_check']);
        $definition->addArgument($config['database']['driver']);
        $definition->addArgument($config['database']['host']);
        $definition->addArgument($config['database']['name']);
        $definition->addArgument($config['database']['port']);
        $definition->addArgument($config['database']['user']);
        $definition->addArgument($config['database']['password']);
        $definition->addArgument($config['database']['table_prefix']);
        $container->setDefinition('aureka_vb.configuration', $definition);
    }


    private function createLoginListenerDefinition(ContainerBuilder $container, array $config)
    {
        $definition = new Definition('Aureka\VBBundle\Event\Listener\LoginListener');
        $definition->setFactoryClass('Aureka\VBBundle\Factory\ServicesFactory');
        $definition->setFactoryMethod('createLogin');
        $definition->addArgument(new Reference('aureka_vb.configuration'));
        $definition->addArgument(new Reference('request_stack'));
        $definition->addTag('kernel.event_listener', array(
            'event' => 'security.authentication.success',
            'method' => 'onAuthenticationSuccess'));
        $definition->addTag('kernel.event_listener', array(
            'event' => 'kernel.response',
            'method' => 'onKernelResponse'));
        $container->setDefinition('aureka_vb.login_listener', $definition);
    }


    private function createLogoutHandlerDefinition(ContainerBuilder $container, array $config)
    {
        $definition = new Definition('Aureka\VBBundle\Handler\LogoutHandler');
        $definition->setFactoryClass('Aureka\VBBundle\Factory\ServicesFactory');
        $definition->setFactoryMethod('createLogout');
        $definition->addArgument(new Reference('aureka_vb.configuration'));
        $definition->addArgument(new Reference('request_stack'));
        $container->setDefinition('aureka_vb.logout_handler', $definition);
    }


    public function getAlias()
    {
        return 'aureka_vb';
    }
}
