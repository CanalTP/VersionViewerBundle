<?php
namespace Bbr\VersionViewerBundle\Applications\URLHandler;

/**
 *
 * @author bbonnesoeur
 *         Url Handler Interface
 */
interface URLHandler
{
    /**
     * Set the host
     *
     * @param string $host            
     */
    public function setHost($host);

    /**
     * return the host
     * 
     * @param string $env  
     * 
     * @return string the host string
     */
    public function getHost($env);

}
