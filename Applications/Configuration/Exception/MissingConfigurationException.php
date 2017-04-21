<?php
namespace Bbr\VersionViewerBundle\Applications\Configuration\Exception;

/**
 *
 * @author bbonnesoeur
 *        
 */
class MissingConfigurationException extends \Exception
{

    public function __construct($message)
    {
        $this->message = $message;
    }
}