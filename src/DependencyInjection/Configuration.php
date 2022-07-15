<?php declare(strict_types=1);

namespace VysokeSkoly\AppStatusBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('vysoke_skoly_app_status');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('source_file')
                    ->defaultValue('var/buildinfo.xml')
                ->end()
                ->scalarNode('main_status_key')
                    ->defaultValue('buildBranch')
                ->end()
                ->scalarNode('env_file')
                    ->defaultValue('/etc/vysokeskoly.xml')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
