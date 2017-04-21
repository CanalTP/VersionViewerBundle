<?php
namespace Bbr\VersionViewerBundle\Applications;

use Bbr\VersionViewerBundle\Applications\URLHandler\URLHandler;
use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile;
use Bbr\VersionViewerBundle\Applications\ApplicationType\ApplicationType;

/**
 * Class for an instance of an application
 *
 * @author bbonnesoeur
 *        
 */
class AppInstance
{

    /**
     *
     * @var Environment environment of the instance
     */
    private $environment;

    /**
     *
     * @var string hostname of the instance
     */
    private $host;

    /**
     *
     * @var ReleaseFile releaseFile of the instance
     */
    private $releaseFile = null;

    /**
     *
     * @var string color of the instance acocrding its comparison value.
     */
    private $color;

    /**
     *
     * @var URLHandler $hostHandler : Urlhandler for this instance
     */
    private $hostHandler;

    /**
     *
     * @param Environment $env
     *            application environment
     * @param URLHandler $urlHandler
     *            handler used by the instance
     * @param ReleaseFile $releaseFile
     *            release file
     * @param ApplicationType $appType
     *            type of application
     * @todo remove this useless parameter, maybe it was useful when subapplication was handled.
     */
    public function __construct(Environment $env, URLHandler $urlHandler, ReleaseFile $releaseFile, ApplicationType $appType)
    {
        $this->environment = $env;
        $this->hostHandler = $urlHandler;
        $this->host = $this->hostHandler->getHost($this->environment->getTrigram());
        
        $releaseFile->setHost($this->host);
        $this->releaseFile = $releaseFile;
    }

    /**
     * load the release file content
     *
     * @return ReleaseFile false : the object if is valid false otherwise
     */
    public function loadVersion()
    {
        $this->releaseFile->load();
        
        // if loading is OK
        if ($this->releaseFile->isValid()) {
            return $this;
        }
        
        return false;
    }

    /**
     *
     * @return Environment instance environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     *
     * @return ReleaseFile the release file of the instance
     */
    public function getReleaseFile()
    {
        return $this->releaseFile;
    }

    /**
     *
     * @param string $color
     *            one of the color defined in ColorManager class
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     *
     * @return string : color of the instance
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * return the release file errors as String
     * remove also HTML tags
     *
     * @return array[string] : release file's errors
     */
    public function getErrorsAsString()
    {
        return strip_tags(implode(",", $this->releaseFile->getErrors()));
    }
}
