<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFile\Exception;

/**
 *
 * @author bbonnesoeur
 *        
 */
class ReleaseFilePropertyNotFoundException extends \Exception
{

    public function __construct($message)
    {
        $this->message = $message;
    }
}