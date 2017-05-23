<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFile;

use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\ReleaseFileConfiguration;
use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\ReleaseFileLoadingException;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Exception\ReleaseFileParsingException;

/**
 *
 * @author bbonnesoeur
 */
class XmlReleaseFile extends ReleaseFile
{

    /**
     *
     * @todo maybe move this in ReleaseFile class if possible
     * @param Environment $env            
     * @param ReleaseFileConfiguration $configuration            
     */
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
        return $this->properties[$this->configuration->getComparisonValue()];
    }

    /**
     *
     * @todo need to be in an abstract super class ? could be same for json xml and text file
     *       (non-PHPdoc) @see \Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile::load()
     */
    public function load()
    {
        try {
            $content = $this->loader->load($this);
        } catch (ReleaseFileLoadingException $e) {
            // @todo add the excpetion message to error message.
            $this->addLoadingError($this->environment);
            $this->setIsValid(false);
            return false;
        }
        
        try {
            $this->filterData($content);
        } catch (ReleaseFileParsingException $e) {
            // @todo add the excpetion message to error message.
            $this->setIsValid(false);
            $this->addParsingError($this->environment);
            return false;
        }
        $this->setIsValid(true);
    }

    /**
     * filter xml properties according configuration and put them into an array.
     *
     * @param string $xml
     *            xml feed
     */
    private function filterData($xml)
    {
        $XmlDocument = new \DOMDocument();
        
        if (! @$XmlDocument->loadXML($xml)) {
            throw new ReleaseFileParsingException('Unable to parse XML content !');
        }
        
        $xpath = new \DOMXPath($XmlDocument);
        // process each configured xpath against xml
        foreach ($this->configuration->getFilteredProperties() as $property => $xpathExpression) {
            
            $resultNode = $xpath->query($xpathExpression);
            
            if ($resultNode && $resultNode->length > 0) {
                $this->properties[$property] = trim($resultNode->item(0)->nodeValue);
            } else {
                
                $this->properties[$property] = 'not found !';
                $this->addUnfoundPropertyWarning($this->environment, $property, $xpathExpression);
            }
        }
    }
}