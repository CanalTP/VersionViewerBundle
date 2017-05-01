<?php

/**
 * Version Viewer application context
 * 
 * Handle all informations and configurations to run the app
 */
namespace Bbr\VersionViewerBundle\Applications;

use Bbr\VersionViewerBundle\Applications\Application;
use Bbr\VersionViewerBundle\Applications\ApplicationType\ApplicationType;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class AppContext
{

    /**
     *
     * @var array applications List
     */
    private $appList = array();

    /**
     *
     * @var array application type list
     */
    private $appTypeList = array();

    /**
     *
     * @var array environment list
     */
    private $envList = array();

    /**
     *
     * @var array URLHandler List
     */
    private $URLHandler = array();

    /**
     *
     * @var array Entire configuration
     */
    public $appconfig;

    /**
     * Create applications which contains informations used for their instranciation
     *
     * @param array $environments
     *            environment configuration
     * @param array $applications
     *            applicaitons configurations
     * @param array $appConfig
     *            VersionViewer configuration
     * @param array $urlHandler
     *            UrlHandler configuration
     * @param array $applicationTypesConfig
     *            application types configuration
     */
    function __construct($environments, $applications, $appConfig, $urlHandlers, $applicationTypesConfig)
    {
        // dump($applicationTypesConfig);die;
        $this->handleAppConfiguration($appConfig);
        
        // init applications types
        foreach ($applicationTypesConfig as $appType) {
            $applicationType = new ApplicationType($appType);
            $this->appTypeList[$applicationType->getId()] = $applicationType;
        }
        
        // init environments
        foreach ($environments as $env) {
            $environment = new Environment($env);
            $this->envList[$environment->getTrigram()] = $environment;
        }
        
        $ApplicationFactory = ApplicationFactory::getInstance();
        
        // init applications
        foreach ($applications as $appkey => $applicationConfig) {
            $application = $ApplicationFactory::getApplication($this->appTypeList[$applicationConfig['appType']], $applicationConfig, $appkey, $this->getEnvList(), $urlHandlers);
            $this->appList[$application->getAppKey()] = $application;
        }
        
        // Sort by alphabétical order conserving key/value
        // @TODO need to pass namespace but avoid to define sort method in this class
        uasort($this->appList, array(
            __NAMESPACE__ . '\Application',
            'sortApplicationByName'
        ));
    }

    /**
     * Entry point for handling application Configuration section
     *
     * @param array $appConfiguration
     *            configuration of versionViwer Application.
     */
    private function handleAppConfiguration($appConfiguration)
    {
        $this->appconfig['feedback_email'] = $this->handleFeedbackEmailConfiguration($appConfiguration);
        $this->appconfig['analytics'] = $this->handleAnalyticsConfiguration($appConfiguration);
    }

    /**
     * Handle feedback email configuration and set default value if not set
     *
     * @throws ParameterNotFoundException if "from" or "to" parameters are not set if feedback enabled
     *        
     * @param
     *            array application configuration section
     *            
     * @return array feedback_email[enabled][true |false]
     *         [to][string]
     *         [from][string]
     */
    private function handleFeedbackEmailConfiguration($configuration = NULL)
    {
        $config = array();
        if (! isset($configuration['feedback_email'])) {
            $config['enabled'] = false;
        } else {
            if (! isset($configuration['feedback_email']['from']) || ! isset($configuration['feedback_email']['to'])) {
                throw new InvalidConfigurationException('You must specify "from" and "to" parameters if you enabled feedback form !');
            }
            
            $config = $configuration['feedback_email'];
        }
        return $config;
    }

    /**
     * Handle analytics configuration and set default value if not set
     *
     * @throws ParameterNotFoundException if "uid" are not set if analytics enabled
     *        
     * @param
     *            array application configuration section
     *            
     * @return array anlytics[enabled][true |false]
     *         [uid][string]
     */
    private function handleAnalyticsConfiguration($configuration = null)
    {
        $config = array();
        if (! isset($configuration['analytics'])) {
            $config['enabled'] = false;
        } else {
            if (! isset($configuration['analytics']['uid'])) {
                throw new InvalidTypeException('You must specify "uid" parameter if you enabled analytics !');
            }
            
            $config = $configuration['analytics'];
        }
        return $config;
    }

    /**
     *
     * @return array application list
     */
    function getAppList()
    {
        return $this->appList;
    }

    /**
     * return the application list the given type
     *
     * @return array[Application] Application list
     *        
     * @param string $type
     *            application type filter
     */
    function getAppListByType($type)
    {
        $appList = array();
        foreach ($this->appList as $appKey => $application) {
            if ($application->appType->getId() == $type) {
                array_push($appList, $application);
            }
        }
        
        return $appList;
    }

    /**
     * return the applicaiton for the given key
     *
     * @param string $appKey
     *            key off the application
     * @return Application|boolean application instance, false otherwise.
     */
    function getApplication($appKey)
    {
        if (array_key_exists($appKey, $this->appList)) {
            return $this->appList[$appKey];
        }
        return false;
    }

    /**
     *
     * @return String mail address from feedback email were send
     */
    public function getFeedBackEmailSender()
    {
        return $this->appconfig['feedback_email']['from'];
    }

    /**
     *
     * @return string mail adress where feedback are send
     */
    public function getFeedBackEmailReceiver()
    {
        return $this->appconfig['feedback_email']['to'];
    }

    /**
     * return environment list
     *
     * @see Environment
     *
     * @return array[Environment] list of environment
     *        
     */
    public function getEnvList()
    {
        return $this->envList;
    }

    /**
     * setter de EnvList
     *
     * @param array $envList            
     */
    public function setEnvList($envList)
    {
        $this->envList = $envList;
    }

    public function getAppTypeList()
    {
        return $this->appTypeList;
    }
}
