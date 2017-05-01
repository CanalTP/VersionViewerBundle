<?php
namespace Bbr\VersionViewerBundle\Applications;

use Bbr\VersionViewerBundle\Applications\ApplicationType\ApplicationType;
use Bbr\VersionViewerBundle\Applications\ApplicationType\DefaultApplication;

/**
 * Handle application creation
 *
 * @author bbonnesoeur
 * @todo comment translation
 *      
 */
class ApplicationFactory
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
     * return a singleton instance of the factory
     *
     * @return Bbr\VersionViewerBundle\Applications\ApplicationFactory singleton de la factory
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new ApplicationFactory();
        }
        return self::$_instance;
    }

    /**
     * Instantiate an application according asked type and parameters
     *
     *
     * @param ApplicationType $appType
     *            asked application type (according config)
     * @param array $applicationConfig
     *            application configuration
     * @param string $appKey
     *            application key (must be unique)
     * @param array $environments
     *            envrionments list (all defined environment even if application is not defined for this environment.)
     * @param array $urlHandlersConfig
     *            URLHandlers configuration
     * @return Application
     *
     */
    public static function getApplication(ApplicationType $appType, $applicationConfig, $appKey, $environments, $urlHandlersConfig)
    {
        if ($appType->hasConfiguredClass()) {
            $className = $appType->getClass();
            return new $className($applicationConfig, $appKey, $environments, $urlHandlersConfig, $appType);
        } else {
            return new DefaultApplication($applicationConfig, $appKey, $environments, $urlHandlersConfig, $appType);
        }
    }
}