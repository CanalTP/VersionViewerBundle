<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFile;

use Bbr\VersionViewerBundle\Applications\ApplicationType\ApplicationType;
use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\ReleaseFileConfiguration;
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\AbstractReleaseFileLoader;
use Bbr\VersionViewerBundle\Applications\URLHandler\URLHandler;

/**
 * Abstract class for release File
 *
 * @author bbonnesoeur
 */
abstract class ReleaseFile
{

    /**
     *
     * @var string $host host file
     */
    protected $host = '';

    /**
     *
     * @var string $filePath release file path containing file name
     */
    protected $filePath = '';

    /**
     *
     * @var array $errors error messages
     */
    private $errors = array();

    /**
     *
     * @var array $warning warning messages
     */
    private $warnings = array();

    /**
     *
     * @var boolean $isvalid set to true if the loading and parsing of the release file is ok
     */
    protected $isValid = false;

    /**
     *
     * @var Environment $environment release file environment
     */
    protected $environment = '';

    /**
     *
     * @var ReleaseFileLoader $loader loader object
     */
    protected $loader;

    /**
     *
     * @var ReleaseFileConfiguration $configuration configuration of release file
     */
    protected $configuration = null;

    /**
     *
     * @var array[string] $properties array of key value to display
     */
    protected $properties = array();

    /**
     * Load release file
     */
    public abstract function load();

    const RELEASE_FILE_LOADING_ERROR = 'Unable to load Release File in <strong>%s</strong> environment !';

    const RELEASE_FILE_PARSING_ERROR = 'Unable to parse Release File in <strong>%s</strong> environment !';

    const RELEASE_FILE_UNFOUND_PROPERTY_ERROR = 'Property <strong>%s</strong> couldn\'t be found in Release File content in <strong>%s</strong> environment !';

    const RELEASE_FILE_UNFOUND_PROPERTY_WARNING = 'Property <strong>%s</strong> (path : %s) couldn\'t be found in Release File content in <strong>%s</strong> environment !';

    /**
     *
     * @return string json encoded string of properties get from releaseFile
     */
    public function getPropertiesJson()
    {
        return json_encode($this->properties);
    }

    /**
     *
     * @return string the Value to be compared as main comparison value
     */
    public function getComparisonValue()
    {
        if (isset($this->properties[$this->configuration->getComparisonValue()]))
            return $this->properties[$this->configuration->getComparisonValue()];
        
        return false;
    }

    /**
     *
     * @return boolean true if file is valid, false otherwise
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     *
     * @param string $host
     *            host of realease file
     *            
     * @return ReleaseFile
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     *
     * @return the string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     *
     * @param string $filePath            
     *
     * @return ReleaseFile
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     *
     * @return string file path
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     *
     * @param boolean $isValid            
     */
    protected function setIsValid($isValid)
    {
        $this->isValid = $isValid;
        return $this;
    }

    /**
     * Return error message
     *
     * @return string error content
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Return warning messages
     *
     * @return array[string] error content
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     *
     * @return Environment environment of the release file
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     *
     * @param Environment $environment            
     * @return ReleaseFile
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * add a loading error message with the environnemnt in it
     *
     * @param Environment $environment            
     */
    public function addLoadingError(Environment $environment)
    {
        $this->addError(sprintf(self::RELEASE_FILE_LOADING_ERROR, $environment->getName()));
    }

    /**
     * add a Parsing error message with the environnemnt in it
     *
     * @param Environment $environment            
     */
    public function addParsingError(Environment $environment)
    {
        $this->addError(sprintf(self::RELEASE_FILE_PARSING_ERROR, $environment->getName()));
    }

    /**
     * add an unfound property error message.
     *
     * @todo add the expression tested on content to get propertie for more informative debug
     *      
     * @param Environment $environment
     *            environment
     * @param string $property
     *            unfound property name
     */
    public function addUnfoundPropertyError(Environment $environment, $property)
    {
        $this->addError(sprintf(self::RELEASE_FILE_UNFOUND_PROPERTY_ERROR, $property, $environment->getName()));
    }

    /**
     * add an unfound property warning message.
     *
     * @todo add the expression tested on content to get propertie for more informative debug
     *      
     * @param Environment $environment
     *            environment
     * @param string $property
     *            unfound property name
     */
    public function addUnfoundPropertyWarning(Environment $environment, $property, $path)
    {
        $this->addWarning(sprintf(self::RELEASE_FILE_UNFOUND_PROPERTY_WARNING, $property, $path, $environment->getName()));
    }

    /**
     * add an error message
     *
     * @param string $message
     *            error message content
     */
    protected function addError($message)
    {
        array_push($this->errors, $message);
    }

    /**
     * add a warning message
     *
     * @param string $message
     *            warning message content
     */
    protected function addWarning($message)
    {
        array_push($this->warnings, $message);
    }

    /**
     *
     * @return boolean true if there is an error, false either
     */
    protected function hasError()
    {
        return (boolean) count($this->errors);
    }

    /**
     * 
     * @return string Command used to retrieve release File information
     */
    public function getCommand()
    {
        return $this->loader->getRessourceCommand();    
    }

    /**
     *
     * @return URLHandler associated URL Handler manage releaseFile
     */
    public function getUrlHanlder()
    {
        return $this->urlHandler;
    }

    /**
     *
     * @param ReleaseFileLoader $releaseFileLoader            
     */
    public function setLoader(AbstractReleaseFileLoader $releaseFileLoader)
    {
        $this->loader = $releaseFileLoader;
    }

    /**
     *
     * @return ReleaseFileConfiguration the ReleaseFileConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     *
     * @param ReleaseFileConfiguration $configuration            
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * handle configuration for the ReleaseFile according default apptype configuration and
     * application overwrite
     *
     * @param $applicationType ApplicationType
     *            ApplicationType
     * @param $overwriteConfiguration array
     *            configuration array for overwrite :
     *            ``̀ 
     *            ...
     *            ReleaseFileOverwrite:
     *            default:
     *            releaseFilePath: /somepath/file.txt
     *            filteredProperties:
     *            propertie-one: path/to/propertie-one
     *            propertie-two: path/to/propertie-two
     *            propertie-three: ~
     *            prod:
     *            releaseFilePath: /another/path/file.txt
     *            ...
     *            ``̀ 
     */
    public function handleConfiguration($applicationType, $overwriteConfiguration = null)
    {
        $this->handleFilePathConfiguration($applicationType->getFilePath(), $overwriteConfiguration);
        
        $this->handleFilteredPropertiesConfiguration($applicationType->getFilteredProperties(), $overwriteConfiguration);
    }

    /**
     * handle all possibilities of filtered properties definition.
     * This configuration can be set at application type level or overriden at application level.
     * This method merge the two source of configuration and remove properties overriden with a ~ value.
     *
     * consider configuration in this order :
     * - application Type configuration
     * - default override configuration
     * - environnement specific override configuration
     *
     * ``̀ 
     * ...
     * ReleaseFileOverwrite:
     * default:
     *
     * filteredProperties:
     * propertie-one: path/to/propertie-one
     * propertie-two: path/to/propertie-two
     * propertie-three: ~
     *
     * ...
     * ``̀ 
     *
     * @param array[string] $filteredProperties
     *            filtered properties as defined in appType configuration
     * @param array[string] $overwriteConfiguration
     *            filtered properties override defined in application configuration.
     */
    private function handleFilteredPropertiesConfiguration($filteredProperties, $overwriteConfiguration = null)
    {
        $this->configuration->setFilteredProperties($filteredProperties);
        
        if ($overwriteConfiguration == null) {
            return $this;
        }
        
        if (isset($overwriteConfiguration['default']) && isset($overwriteConfiguration['default']['filteredProperties'])) {
            $this->getConfiguration()->mergeFilteredProperties($overwriteConfiguration['default']['filteredProperties']);
            
            $this->searchAndRemoveFilteredProperties($overwriteConfiguration['default']['filteredProperties']);
        }
        
        if (isset($overwriteConfiguration[$this->getEnvironment()->getTrigram()]) && isset($overwriteConfiguration[$this->getEnvironment()->getTrigram()]['filteredProperties'])) {
            $this->getConfiguration()->mergeFilteredProperties($overwriteConfiguration[$this->getEnvironment()
                ->getTrigram()]['filteredProperties']);
            
            $this->searchAndRemoveFilteredProperties($overwriteConfiguration[$this->getEnvironment()
                ->getTrigram()]['filteredProperties']);
        }
    }

    /**
     * Search for a filtered Properties to remove from configuration.
     * search for the 'null' value (ie :'~' in configuraiton file) in filtered properties, in this case the properties will be removed.
     *
     * propertie-one: path/to/propertie-one
     * propertie-two: path/to/propertie-two
     * propertie-three: ~
     *
     * @param array[string] $overwriteFilteredProperties
     *            arrary of the filterd properties
     */
    private function searchAndRemoveFilteredProperties($overwriteFilteredProperties)
    {
        $propertiesToRemove = array_keys($overwriteFilteredProperties, null, true);
        
        if (count($propertiesToRemove) > 0) {
            $this->getConfiguration()->removeFilteredProperties($propertiesToRemove);
        }
    }

    /**
     * handle all possibilities to configure file path and choose the good one.
     *
     * consider configuration in this order :
     * - application Type configuration
     * - default override configuration
     * - environnement specific override configuration
     *
     * @param $appTypeFilePath string
     *            file path defined at the application Type level
     * @param $overwriteConfiguration array
     *            configuration array for file path overwrite :
     *            ```json
     *            ...
     *            ReleaseFileOverwrite:
     *            default:
     *            releaseFilePath: /somepath/file.txt
     *            prod:
     *            releaseFilePath: /another/path/file.txt
     *            ...
     *            ```
     *            
     * @todo NEED REVIEW if releaseFilePath is only used for releasefile over HTTP => need to be moved in httpLoader.
     *      
     * @return ReleaseFile the release file instance.
     *        
     */
    private function handleFilePathConfiguration($appTypeFilePath, $overwriteConfiguration = null)
    {
        $this->setFilePath($appTypeFilePath);
        
        if (is_null($overwriteConfiguration)) {
            return $this;
        }
        
        if (isset($overwriteConfiguration['default']) && isset($overwriteConfiguration['default']['releaseFilePath'])) {
            $this->setFilePath($overwriteConfiguration['default']['releaseFilePath']);
        }
        
        if (isset($overwriteConfiguration[$this->getEnvironment()->getTrigram()]) && isset($overwriteConfiguration[$this->getEnvironment()->getTrigram()]['releaseFilePath'])) {
            $this->setFilePath($overwriteConfiguration[$this->getEnvironment()
                ->getTrigram()]['releaseFilePath']);
        }
        
        return $this;
    }

    /**
     *
     * @param array $properties            
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
        return $this;
    }
}