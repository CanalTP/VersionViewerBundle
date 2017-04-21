<?php
namespace Bbr\VersionViewerBundle\Applications\URLHandler;

use Bbr\VersionViewerBundle\Applications\URLHandler\URLHandler;
use Bbr\VersionViewerBundle\Applications\Environment;

/**
 * Class to handle templated host.
 * ie : myappname.my_env.mydomain
 *
 *
 * @author bbonnesoeur
 *        
 */
class TemplatedHostURLHandler extends AbstractURLHandler
{

    private $envHosts = array();

    private $envHostSuffix = array();

    /**
     *
     * @param array[string] $param
     *            configuration array for this handler
     */
    public function __construct($param)
    {
        $this->envHosts = $param['envHosts'];
        $this->envHostSuffix = $param['envHostSuffix'];
    }
    
    /**
     *
     * {@inheritdoc}
     *
     * @see \Bbr\VersionViewerBundle\Applications\URLHandler\URLHandler::getUrl()
     */
    public function getHost($env)
    {
        return $this->host . '.' . $this->envHosts[$env] . $this->envHostSuffix;
    }
    
}
