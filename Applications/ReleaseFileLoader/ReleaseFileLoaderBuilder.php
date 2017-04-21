<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFileLoader;

use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ApplicationType\ApplicationType;
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\NrpeReleaseFileLoader;

/**
 * Build the releaseFIleLoader instance according parameters
 *
 * @author bbonnesoeur
 *        
 */
class ReleaseFileLoaderBuilder
{

    private $environment;

    private $application;

    private $applicationType;

    private $releaseFileLoaderType;

    public function __construct(ApplicationType $applicationType, Environment $environment)
    {
        $this->environment = $environment;
        $this->releaseFileLoaderType = $applicationType->getReleaseFileLoaderType();
        $this->applicationType = $applicationType;
    }

    /**
     * Build the release file loader according the app type configuration
     *
     * @see ApplicationType
     *
     * @return HttpReleaseFileLoader|NrpeReleaseFileLoader
     */
    public function build()
    {
        if ($this->releaseFileLoaderType == null) {
            return new HttpReleaseFileLoader($this->environment);
        }
        
        return new NrpeReleaseFileLoader($this->environment);
    }
}