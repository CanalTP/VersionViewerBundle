<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFile\Exception;

/**
 * Thrown in case of an unfound property
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