<?php

namespace Bbr\VersionViewerBundle\Applications\AuthenticationHandler;

use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\AbstractReleaseFileLoader;

/**
 * handle authorization by URL parameter
 * @author bbonnesoeur
 */
class URLParameter implements AuthenticationHandler{

    /**@var parameters */
    private $params;

    /**
     * @param array[] $params key value of URL param
     */
    public function __construct($params){
        $this->params = $params;
    }

    public function authenticate(AbstractReleaseFileLoader $loader){

        foreach($this->params as $key => $value){
            if(strpos($loader->getUrl(), '?')){
                $paramSeparator = '&';
            }
            else{
                $paramSeparator = '?';
            }

            $loader->addUrlParam($paramSeparator.$key.'='.$value);
        }
    }
}