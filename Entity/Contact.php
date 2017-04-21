<?php
namespace Bbr\VersionViewerBundle\Entity;

// use Symfony\Component\Validator\Mapping\ClassMetadata;
// use Symfony\Component\Validator\Constraints\NotBlank;
/**
 *
 * Elements du formulaire de contact
 *
 * @author bbonnesoeur
 *        
 */
class Contact
{
    protected $name;
    
    protected $body;
    
//     public static function loadValidatorMetadata(ClassMetadata $metadata)
//     {
//         $metadata->addPropertyConstraint('name', new NotBlank());
    
//         $metadata->addPropertyConstraint('body', new NotBlank());
//     }

    /**
     *
     * @return the unknown_type
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param unknown_type $name            
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     *
     * @return the unknown_type
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     *
     * @param unknown_type $body            
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }
 
    
}