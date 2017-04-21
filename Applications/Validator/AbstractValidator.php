<?php
namespace Bbr\VersionViewerBundle\Applications\Validator;


use Bbr\VersionViewerBundle\Applications\Validator\ValidatorInterface;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile;

/**
 * classe conteneur de validation contient les messages à afficher.
 *
 * @author bbonnesoeur
 *        
 */
abstract class AbstractValidator 
{
    //même instance injecter à chaque validateur
    static $validatorManager;
    
    
    const ERROR = 'error';
    const WARNING = 'warning';
    const INFO = 'info';
    
    
    
    public function __construct(ValidatorManager $vm){
      self::$validatorManager = $vm;
    }
    
    /**
     * ajoute un message selon le niveau passé en paramètre
     * @param string $level niveau du message parmis VersionValidator::ERROR, VersionValidator::WARNING, VersionValidator::INFO
     * @param string $message le message
     */
    protected function addMessage($level=self::INFO, $message){
        self::$validatorManager->addMessage($level, $message);
    }
    
    /**
    * retourne tous les messages (error, warning, info)
    * @return array tableau contenant les tableuax de chaque niveau d'erreur
    */
    public function getMessages(){
        return self::$validatorManager->getMessages();
    }
    
    public abstract function validate(ReleaseFile $file1, ReleaseFile $file2=null);
    
    
    
}