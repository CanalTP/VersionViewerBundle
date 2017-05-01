<?php
namespace Bbr\VersionViewerBundle\Entity;

// use Symfony\Component\Validator\Mapping\ClassMetadata;
// use Symfony\Component\Validator\Constraints\NotBlank;
/**
 *
 * Contact form entity
 *
 * @author bbonnesoeur
 *        
 */
class Contact
{

    /**
     *
     * @var string name
     */
    protected $name;

    /**
     *
     * @var string email
     */
    protected $email;

    /**
     *
     * @var string message
     */
    protected $body;

    /**
     *
     * @return string the name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param string $name            
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     *
     * @return string the body message
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     *
     * @param string $body            
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     *
     * @return string the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     *
     * @param string $email            
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
}