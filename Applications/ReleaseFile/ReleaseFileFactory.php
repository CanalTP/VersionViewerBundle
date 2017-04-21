<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFile;

use Bbr\VersionViewerBundle\Applications\ApplicationType\ApplicationType;
use Bbr\VersionViewerBundle\Applications\Environment;

/**
 *
 * @author bbonnesoeur
 *         responsible of instanciation of AuthenticationHanlder
 * @todo comment translation
 */
class ReleaseFileFactory
{

    private static $_instance = null;

    /**
     * prevent instanciation
     *
     * @return null
     */
    private function __construct()
    {
        return null;
    }

    /**
     * return a singleton of the factory
     *
     * @return ReleaseFileFactory
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new ReleaseFileFactory();
        }
        return self::$_instance;
    }

    /**
     * Instanciate a release accordingly type and paramter
     *
     * @param ApplicationType $appType
     *            application type Which determine release file type to instanciate
     * @param Environment $env
     *            Environment for this release file
     *            
     * @return ReleaseFile the release file
     *        
     * @throws \InvalidArgumentException if type is unknown
     */
    public static function getReleaseFile(ApplicationType $appType, Environment $env)
    {
        switch ($appType->getFileType()) {
            case 'json':
                return new JsonReleaseFile($env, clone $appType->getFileConfiguration());
                break;
            case 'xml':
                return new XmlReleaseFile($env, clone $appType->getFileConfiguration());
                break;
            case 'text':
            case 'txt':
                return new TextReleaseFile($env, clone $appType->getFileConfiguration());
                break;
            default:
                throw new \InvalidArgumentException("Release File type not supported : '" . $appType->getFileType() . "' ! \nValid configuration values : json, xml, text", 500);
        }
    }
}
