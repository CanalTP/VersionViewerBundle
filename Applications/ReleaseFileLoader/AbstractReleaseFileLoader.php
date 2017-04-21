<?php

namespace Bbr\VersionViewerBundle\Applications\ReleaseFileLoader;

use Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile;

/**
 * @author bbonnesoeur
 *
 */
abstract class AbstractReleaseFileLoader implements ReleaseFileLoaderInterface
{
    
    /**
     *
     * @var AuthenticationHandler authentication Handler
     */
    private $authenticationHandler;
    
    /**
     *
     * @var Environment environment. Used to see if a specific configuration is set (ex: timeout, ...)
     */
    private $environment;
    
    /**
     * 
     * @var ReleaseFile releaseFileInstance
     */
    protected $releaseFile;
    
    /**
     *
     * {@inheritdoc}
     *
     * @see ReleaseFileLoaderInterface::load()
     * 
     * @throws ReleaseFileLoadingException
     */
    abstract public function load();
    
    abstract public function handleConfiguration($configuration);
    
    abstract public function getRessourceCommand();
    
    /**
     * 
     * @param ReleaseFile $releaseFile the release File associated
     */
    public function setReleaseFile(ReleaseFile $releaseFile){
        $this->releaseFile = $releaseFile;
    }
    
    /**
     * set the authentication Handler
     */
    public function setAuthenticationHandler($authenticationHandler)
    {
        $this->authenticationHandler = $authenticationHandler;
    }
}