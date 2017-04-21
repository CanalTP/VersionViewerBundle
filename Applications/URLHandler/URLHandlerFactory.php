<?php
namespace Bbr\VersionViewerBundle\Applications\URLHandler;

use Bbr\VersionViewerBundle\Applications\Configuration\Exception\MissingConfigurationException;
use Bbr\VersionViewerBundle\Applications\URLHandler\FullHostURLHandler;

/**
 * Build an URLHandlee raccroding parameters
 * 
 * @author bbonnesoeur
 *        
 * @todo must be renamed in hostHandler.
 *      
 */
class URLHandlerFactory
{

    private static $_instance = null;

    /**
     * avoid instantiation
     *
     * @return null
     */
    private function __construct()
    {}

    /**
     * return a singleton of the factory
     *
     * @return URLHandlerFactory
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new URLHandlerFactory();
        }
        return self::$_instance;
    }

    /**
     * instantiate a host handler (old name url) according requested type and params
     * by default instantiate a FUllHost handler
     *
     * @param string $type
     *            requested URLhandler Type (config)
     * @param array $param
     *            configuration options.
     * @return URLHandler
     */
    public static function getURLHandler($urlHandlerType, $param)
    {
        switch ($urlHandlerType) {
            case 'TemplatedHostURLHandler':
                return new TemplatedHostURLHandler($param);
                break;
            case 'Custom':
                if (! isset($param['class'])) {
                    throw new MissingConfigurationException("You need to provide a 'class' configuration if you use the 'Custom' URLHandler type in the URLHandler definition");
                }
                return new $param['class']($param);
                break;
            case 'FullHostURLHandler':
            default:
                return new FullHostURLHandler($param);
        }
    }
}
