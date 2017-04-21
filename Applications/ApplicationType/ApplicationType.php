<?php
namespace Bbr\VersionViewerBundle\Applications\ApplicationType;

use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\ReleaseFileConfiguration;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\JsonPropertieTransformer;
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\ReleaseFileLoaderType;

/**
 * Represent an application type
 *
 * @author bbonnesoeur
 */
class ApplicationType
{

    /**
     *
     * @var string $id application identifier.
     *      must be unique accross all application Type
     */
    private $id = '';

    /**
     *
     * @var string $name name of the type
     */
    private $name = '';

    /**
     *
     * @var string $releaseFilePath path to the release file
     */
    private $filePath = '';

    /**
     *
     * @var string $fileType type of file json, xml, txt
     */
    private $fileType = '';

    /**
     *
     * @var ReleaseFileConfiguration $fileConfiguration configuration for the type of application
     */
    private $fileConfiguration;

    /**
     * file loader type reference in configuration.
     * if none left empty.
     * 
     * @var string
     */
    private $releaseFileLoaderType = null;

    /**
     * Class which handle the type. Full Namespace must be specified 
     * ie : Abc\MyBundle\Applications\ApplicationType\MyApplication
     * left empty if not specified in configuration.
     * 
     * @var string | null
     */
    private $class = null;
    
    /**
     * constructor
     *
     * @todo need some refacto
     *      
     * @param array $appTypeConfig
     *            configuration for the application type.
     */
    public function __construct($appTypeConfig)
    {
        $this->id = $appTypeConfig['id'];
        $this->name = $appTypeConfig['name'];
        $this->filePath = $appTypeConfig['releaseFilePath'];
        
        // if release file for this type is loaded over HTTP (default config) configuration of this param can be omitted
        if (isset($appTypeConfig['releaseFileLoader'])) {
            $this->releaseFileLoaderType = $appTypeConfig['releaseFileLoader'];
        }
        
        if(isset($appTypeConfig['class'])){
            $this->class = $appTypeConfig['class'];
        }
        
        // @todo add a check on value here (xml, json, text,txt )
        $this->fileType = $appTypeConfig['fileType'];
        
        $this->handleReleaseFileConfiguration($appTypeConfig);
    }

    /**
     * Handle Release File Configuration section in application type configuration
     * - filtered properties
     * - comparison value
     *
     * @param array[] $appTypeConfig
     *            apptype configuration
     */
    private function handleReleaseFileConfiguration($appTypeConfig)
    {
        $this->fileConfiguration = new ReleaseFileConfiguration();
        
        if ($this->fileType == 'json') {
            $this->fileConfiguration->setPropertieTransformer(new JsonPropertieTransformer());
        }
        
        // @todo add an exception if no value is defined
        if (isset($appTypeConfig['filteredProperties'])) {
            $this->fileConfiguration->setFilteredProperties($appTypeConfig['filteredProperties']);
        }
        // @todo add an exception if no value defined in configuration
        if (isset($appTypeConfig['comparisonValue'])) {
            $this->fileConfiguration->setComparisonValue($appTypeConfig['comparisonValue']);
        }
    }

    /**
     *
     * @return string the type id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return the string name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @return the string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     *
     * @return the string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     *
     * @return array[string] the json propeties to retrieve
     */
    public function getFilteredProperties()
    {
        return $this->getFileConfiguration()->getFilteredProperties();
    }

    /**
     *
     * @return the ReleaseFileConfiguration
     */
    public function getFileConfiguration()
    {
        return $this->fileConfiguration;
    }

    /**
     *
     * @return the ReleaseFileLoaderType
     */
    public function getReleaseFileLoaderType()
    {
        return $this->releaseFileLoaderType;
    }

    /**
     *
     * @param ReleaseFileLoaderType $releaseFileLoaderType            
     */
    public function setReleaseFileLoaderType(ReleaseFileLoaderType $releaseFileLoaderType)
    {
        $this->releaseFileLoaderType = $releaseFileLoaderType;
        return $this;
    }

    /**
     *
     * @return the string
     */
    public function getClass()
    {
        return $this->class;
    }
    /**
     * @return true if a class has been configured for this application type
     */
    public function hasConfiguredClass(){
       if($this->class === null){
           return false;
       }
       else{
           return true;
       }
    }
 
}