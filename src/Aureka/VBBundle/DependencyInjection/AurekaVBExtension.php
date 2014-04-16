<?php

namespace Aureka\VBBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

use Aureka\VBBundle\VBConfiguration;

class AurekaVBExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $config_definition = new Definition('Aureka\VBBundle\VBConfiguration');
        $config_definition->addArgument($config['license']);
        $config_definition->addArgument($config['default_user_group']);
        $config_definition->addArgument($config['cookie_prefix']);
        $config_definition->addArgument($config['ip_check']);
        $config_definition->addArgument($config['database']['driver']);
        $config_definition->addArgument($config['database']['host']);
        $config_definition->addArgument($config['database']['name']);
        $config_definition->addArgument($config['database']['port']);
        $config_definition->addArgument($config['database']['user']);
        $config_definition->addArgument($config['database']['password']);
        $config_definition->addArgument($config['database']['table_prefix']);
        $container->setDefinition('aureka_vb.configuration', $config_definition);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }


    public function getAlias()
    {
        return 'aureka_vb';
    }
}
