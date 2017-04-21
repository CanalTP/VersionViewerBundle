<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFileLoader;
/**
 * @author bbonnesoeur
 *
 */
class ReleaseFileLoadingException extends \Exception
{
    
    
    public function __construct($message){
        
        if (is_array($message)){
            $this->message = $message[0];
        }
        else{
            $this->message = $message;
        }
    }
    
}