<?php

namespace Artgris\Bundle\EasyAdminCommandsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('artgris_easy_admin_commands');
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('dir')
                    ->defaultValue('%kernel.project_dir%/config/packages/easy_admin/entities/')
                ->end()
                ->arrayNode('namespaces')
                    ->defaultValue(['App\Entity'])
                    ->scalarPrototype()
                    ->end()
                ->end()
                ->arrayNode('entities')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('excluded')
                            ->scalarPrototype()
                            ->end()
                        ->end()
                        ->arrayNode('included')
                            ->scalarPrototype()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('types')
                    ->normalizeKeys(false)
                    ->defaultValue([])
                    ->prototype('variable')
                    ->end()
                ->end()
                ->arrayNode('regex')
                    ->normalizeKeys(false)
                    ->defaultValue([])
                    ->prototype('variable')
                    ->end()
                ->end()
                ->arrayNode('list')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('excluded')
                            ->scalarPrototype()
                            ->end()
                        ->end()
                        ->arrayNode('included')
                            ->scalarPrototype()
                            ->end()
                        ->end()
                        ->arrayNode('position')
                            ->scalarPrototype()
                            ->end()
                        ->end()
                        ->arrayNode('sort')
                            ->normalizeKeys(false)
                            ->defaultValue([])
                            ->prototype('variable')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('excluded')
                            ->scalarPrototype()
                            ->end()
                        ->end()
                        ->arrayNode('included')
                            ->scalarPrototype()
                            ->end()
                        ->end()
                        ->arrayNode('position')
                            ->scalarPrototype()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
