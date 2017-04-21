<?php
namespace Bbr\VersionViewerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
/**
 *
 * @author bbonnesoeur
 *        
 */
class ContactType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder->add('name', 'text', array('attr' => array('class'=>'form-control')));
        $builder->add('body', 'textarea',array('attr' => array('class'=>'form-control')));
    }
    
    public function getName()
    {
        return 'contact';
    }
}