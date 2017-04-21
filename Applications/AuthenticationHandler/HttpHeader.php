<?php
namespace Bbr\VersionViewerBundle\Applications\AuthenticationHandler;

use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\AbstractReleaseFileLoader;

/**
 * handle http header authorization
 * 
 * @author bbonnesoeur
 */
class HttpHeader implements AuthenticationHandler
{

    /**@var paramètres de l'option */
    private $params;

    /**
     *
     * @param array[] $params
     *            key value of http Header
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    public function authenticate(AbstractReleaseFileLoader $loader)
    {
        foreach ($this->params as $key => $value) {
            $loader->setHttpContextOption('header', $key . ':' . $value);
        }
    }
}