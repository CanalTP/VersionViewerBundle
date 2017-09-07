<?php

namespace Bbr\VersionViewerBundle\DependencyInjection;

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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bbr_version_viewer');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        
        $rootNode
        //appConfiguration
        ->children()
	        ->arrayNode('appConfig')
		        ->prototype('array')
			        ->children()
// 			        @todo remove this 
				        ->scalarNode('version')
					        ->info("Version Viewer Bundle version (refacto needed")
					        ->example('0.15')
				        ->end()
				        ->arrayNode('feedback_email')
					        ->canBeDisabled()
					        ->addDefaultsIfNotSet()
				        	->info("Feedback form email setting")
			        		->children()
			        			->booleanNode('enabled')
			        				->defaultValue('false')
			        				->isRequired()->cannotBeEmpty()
			        			->end()
			        			->scalarNode('to')
				        			->info("email to")
			        			->end()
			        			->scalarNode('from')
				        			->info("email from")
				        			->isRequired()->cannotBeEmpty()
			        			->end()
			        		->end()
						->end()
			        ->end()
		        ->end()
	        ->end()
        ->end()
        //environnements
        ->children()
          ->arrayNode('environments')
            ->prototype('array')
              ->children()
                ->scalarNode('name')
                  ->info("nom de l'environnement")
                  ->example('intégration')
                ->end()
                ->scalarNode('trigram')
                  ->info("trigram de l'environnement")
                  ->example('itg')
                ->end()
              ->end()
            ->end()
          ->end()
        ->end()
        //urlHandler
        //->append($this->processUrlHandler())
        //applications
        ->children()
          ->arrayNode('applications')
            ->prototype('array')
              ->children()
              ->scalarNode('appName')
                ->info("nom de l'application")
              ->end()
              ->scalarNode('appHost')
                ->info("première partie du NDD")
              ->end()
            ->end()
          ->end()
        ->end()
        ;
        
        
        
        return $treeBuilder;
    }
    
    /**
     * valide la configuration de la partie URLHandler
     */
    public function processUrlHandler(){
      
      $builder = new TreeBuilder();
      $node = $builder->root('urlHandler');
      
      $node
        ->isRequired()
          ->requiresAtLeastOneElement()
          //->useAttributeAsKey('name')
          ->prototype('array')
            ->children()
              
            ->end()
          ->end()
        ->end()
      ;
      
    }
}
