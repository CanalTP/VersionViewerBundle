<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFileLoader;

/**
 * Interface for releaseFileLoader
 *
 * @author bbonnesoeur
 *        
 */
interface ReleaseFileLoaderInterface
{

    /**
     *
     * @param string $param
     *            paramater needed to load the release file
     */
    public function load();

    /**
     *
     * @param array[] $configuration            
     */
    public function handleConfiguration($configuration);

    /**
     * return the command used to retrieve the release file according the releaseFileLoaderType (HTTP, NRPE, ...)
     *
     * @return string the command to retrieve the ressource
     */
    public function getRessourceCommand();
}