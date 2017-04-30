<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFile;

use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\ReleaseFileConfiguration;
use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\ReleaseFileLoadingException;

/**
 * Parse text release File against regexp
 */
class TextReleaseFile extends ReleaseFile
{

    /**
     *
     * @param Environment $env
     *            environment for this release file
     */
    function __construct(Environment $env, ReleaseFileConfiguration $configuration)
    {
        $this->environment = $env;
        $this->configuration = $configuration;
    }

    /**
     *
     * @todo need to be in an abstract super class ? could be same for json xml and text file
     *      
     *       (non-PHPdoc) @see \Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile::load()
     */
    public function load()
    {
        try {
            $content = $this->loader->load($this);
        } catch (ReleaseFileLoadingException $e) {
            // @TODO gérer correctement l'exception et passer son contenu au message
            $this->addLoadingError($this->environment);
            return false;
        }
        
        $this->filterData($content);
        
        $this->setIsValid(true);
    }

    /**
     * filter content against regexp in configuration and put them into an array
     *
     * @todo maybe rename in this method in extractData ?
     * @param string $content
     *            text feed
     */
    private function filterData($content)
    {
        
        // var_dump($this->configuration->getTransformedFilteredProperties ());die;
        foreach ($this->configuration->getTransformedFilteredProperties() as $property => $regexp) {
            
            if (preg_match("/" . $regexp . "/", $content, $match)) {
                $this->properties[$property] = $match[1];
            } else {
                $this->addUnfoundPropertyWarning($this->environment, $property, $regexp);
            }
        }
    }
}
