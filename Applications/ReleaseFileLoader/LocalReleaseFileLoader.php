<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFileLoader;

/**
 *
 * @author bbonnesoeur
 * Load a file on the file system. /!\ only used as a helper for testing purpose /!\          
 */
class LocalReleaseFileLoader extends AbstractReleaseFileLoader
{
    /**
     * 
     * @var string path to the file to load
     */
    private $filePath;
    
    public function __construct($filePath){
        $this->filePath = $filePath;
    }
    
    public function load(){
        
        $content = @file_get_contents($this->filePath);
        if (! $content) {
            throw new ReleaseFileLoadingException("Local file couldn't be found. Error !! : path $this->filepath");
        }
        return $content;
    }
    
    public function handleConfiguration($configuration){
        return;
    }
    
    public function getRessourceCommand(){
        return;
    }
}