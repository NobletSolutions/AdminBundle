<?php


namespace NS\AdminBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ns_admin');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode->children()->scalarNode('base_template')->defaultValue('base.html.twig')->end();

        return $treeBuilder;
    }
}
