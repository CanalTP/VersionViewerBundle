<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration;

/**
 *
 * @author bbonnesoeur
 *
 */
interface ReleaseFileConfigurationInterface
{
    /**
     * return the property against to compare instance
     */
    public function getComparisonValue();

    /**
     * return the properties to get from release file
     */
    public function getFilteredProperties();

}