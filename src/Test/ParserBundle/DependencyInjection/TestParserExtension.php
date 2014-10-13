<?php

namespace Test\ParserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TestParserExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set default parameters
        $container->setParameter('test_parser.urls.base_url', $config['urls']['base_url']);
        $container->setParameter('test_parser.urls.regions_url', $config['urls']['regions_url']);
        $container->setParameter('test_parser.urls.action_list_url', $config['urls']['action_list_url']);
        $container->setParameter('test_parser.urls.action_list_url_params', $config['urls']['action_list_url_params']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
