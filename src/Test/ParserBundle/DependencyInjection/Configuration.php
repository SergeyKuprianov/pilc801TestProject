<?php

namespace Test\ParserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

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
        $rootNode = $treeBuilder->root('test_parser');

        $this->addUrlsSettingsSection($rootNode);

        return $treeBuilder;
    }

    private function addUrlsSettingsSection(ArrayNodeDefinition $node) {
        $node
            ->children()
                ->arrayNode('urls')
                    ->children()
                        ->scalarNode('base_url')->isRequired()->defaultValue('http://www.perekrestok.ru')->end()
                        ->scalarNode('regions_url')->isRequired()->defaultValue('/actions')->end()
                        ->scalarNode('action_list_url')->isRequired()->defaultValue('/list?')->end()
                        ->scalarNode('action_list_url_params')->isRequired()->defaultValue('op=showAll&action=2&param={region}&currentPage={page}')->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
