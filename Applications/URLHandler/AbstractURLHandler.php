<?php
namespace Bbr\VersionViewerBundle\Applications\URLHandler;

use Bbr\VersionViewerBundle\Applications\URLHandler\URLHandler;

/**
 *
 * @author bbonnesoeur
 *        
 */
abstract class AbstractURLHandler implements URLHandler
{

    /**
     *
     * @var string host
     */
    protected $host;

    /**
     *
     * {@inheritdoc}
     *
     * @see \Bbr\VersionViewerBundle\Applications\URLHandler\URLHandler::setHost()
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Bbr\VersionViewerBundle\Applications\URLHandler\URLHandler::getHost()
     */
    public abstract function getHost($env);
}