<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFile;

use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\ReleaseFileConfiguration;
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\ReleaseFileLoadingException;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Exception\ReleaseFilePropertyNotFoundException;

/**
 *
 * @author bbonnesoeur
 */
class JsonReleaseFile extends ReleaseFile
{

    function __construct(Environment $env, ReleaseFileConfiguration $configuration)
    {
        $this->setEnvironment($env);
        $this->configuration = $configuration;
    }

    /**
     *
     * @todo need to be in an abstract super class ? could be same for json xml and text file
     *       (non-PHPdoc) @see \Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile::getComparisonValue()
     */
    public function getComparisonValue()
    {
        if (isset($this->properties[$this->configuration->getComparisonValue()]))
            return $this->properties[$this->configuration->getComparisonValue()];
        
        return false;
    }

    /**
     *
     * @todo need to be in an abstract super class ? could be same for json xml and text file
     *       (non-PHPdoc) @see \Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile::load()
     */
    public function load()
    {
        // @TODO gérer correctement l'exception et passer son contenu au message
        try {
            $content = $this->loader->load($this);
        } catch (ReleaseFileLoadingException $e) {
            // @todo add the message exception to the error message
            $this->addLoadingError($this->environment);
            return false;
        }
        
        try {
            $this->filterData($content);
        } catch (Exception $e) {}
        
        $this->setIsValid(true);
    }

    /**
     * filter json property according to configuration and put them into an array
     * /!\ handle object only !
     *
     * @param string $content
     *            json feed
     *            
     * @todo gérer une récursivité. Ne peut parser que les propriétés simples de premier niveau qui ne
     *       sont pas des objets pour le moment.
     */
    private function filterData($content)
    {
        $json = json_decode($content);
        
        foreach ($this->configuration->getTransformedFilteredProperties() as $property => $path) {
            
            $jsonObject = $json;
            // needed for handle properties with '/' within
            
            try {
                foreach ($path as $p) {
                    
                    if (isset($jsonObject->{$p})) {
                        $jsonObject = $jsonObject->{$p};
                        $this->properties[$property] = $jsonObject;
                    } else {
                        //if we don't found status for status.error, exit for loop and add a warning in catch block.
                        throw new ReleaseFilePropertyNotFoundException('propertie '.$property.'not found ! Actual path : '.$p);
                    }
                }
            } catch (ReleaseFilePropertyNotFoundException $e) {
                $this->addUnfoundPropertyWarning($this->environment, $property, $this->configuration->getFilteredPropertyDefinition($property));
                $this->properties[$property] = 'not found !';
            }
        }
    }
}