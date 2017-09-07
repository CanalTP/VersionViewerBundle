<?php
namespace Bbr\VersionViewerBundle\Applications;

use Bbr\VersionViewerBundle\Applications\URLHandler\URLHandlerFactory;
use Bbr\VersionViewerBundle\Applications\AuthenticationHandler\AuthenticationHandlerFactory;
use Bbr\VersionViewerBundle\Applications\AppInstance;
use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\VersionManager;
use Bbr\VersionViewerBundle\Applications\ColorManager;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFileFactory;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFileLoader;
use Bbr\VersionViewerBundle\Applications\Validator\VersionValidator;
use Bbr\VersionViewerBundle\Applications\Validator\ValidatorManager;
use Bbr\VersionViewerBundle\Applications\ApplicationType\ApplicationType;
use Bbr\VersionViewerBundle\Applications\AuthenticationHandler\AuthenticationHandler;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\ReleaseFileLoaderBuilder;
use Bbr\VersionViewerBundle\DependencyInjection\Configuration;

/**
 *
 * Classe abstraite représentant une application.
 * Elle contient l'ensemble des instances pour chaque environnement.
 *
 * @author bbonnesoeur
 * @todo translate
 *
 */
abstract class Application
{
    
    public $appName;
    
    public $appKey;
    
    public $appType;
    
    // tableau des instances d'app avec en clé l'environnement
    public $appInstances = array();
    
    protected $versionManager;
    
    protected $versionValidator;
    
    // gère les messages des validateurs.
    protected $validatorManager;
    
    // configuration des URL Handler
    public $urlHandler = array();
    
    /**
     *
     * @var array[Environment] List of all available Environments in configuration (not only for this particular application)
     * @todo should be moved in an application builder
     */
    private $environments = array();
    
    /**
     *
     * @var array[string] List of all available URLHandlers (not only for this particular application)
     * @todo should be moved in an application builder
     */
    private $urlHandlersConfigs = array();
    
    /**
     * Instancie chaque version de l'application selon les environnements définis dans la configruation
     *
     * @param array $applicationConfig
     *            : la configuration de l'application
     * @param string $appKey
     *            la clé de l'application un nom unique issue de la configuration (clé associé à l'applicationConfig)
     * @param array[array] $environments
     *            tableau les objets environnements
     * @param array[array] $urlHandlersConfig
     *            configuration des URLHandlers
     * @param ApplicationType $appType
     *            type d'application qui permet de savoir quelles sous applications instancier
     */
    function __construct($applicationConfig, $appKey, $environments, $urlHandlersConfig, ApplicationType $appType)
    {
        
        $this->appName = $applicationConfig['appName'];
        $this->appKey = $appKey;
        $this->appType = $appType;
        $this->environments = $environments;
        $this->urlHandlersConfigs = $urlHandlersConfig;
        
        $this->versionManager = new VersionManager(new ColorManager());
        // création du validateur de version
        $this->validatorManager = new ValidatorManager();
        // TODO les validateurs devraient être instanciés par la classe fille selon la configuration
        // c'ets le type d'application qui devrait porter les validateurs à appliquer.
        $this->versionValidator = new VersionValidator($this->validatorManager);
        
        $this->urlHandler = $applicationConfig['URLHandler'];
        
        $this->CheckApplicationConfiguration($applicationConfig);
        
        // @todo better handling for this, it's ugly !
        $authHandlerConfig = null;
        if (isset($applicationConfig['AuthorizationHandler'])) {
            $authHandlerConfig = $applicationConfig['AuthorizationHandler'];
        }
        // @todo better handling for this too, it's ugly !
        $releaseFileOverwriteConfig = null;
        if (isset($applicationConfig['ReleaseFileOverwrite'])) {
            $releaseFileOverwriteConfig = $applicationConfig['ReleaseFileOverwrite'];
        }
        
        // @todo better handling for this too, it's ugly !
        $releaseFileLoaderConfig = null;
        if (isset($applicationConfig['ReleaseFileLoader'])) {
            $releaseFileLoaderConfig = $applicationConfig['ReleaseFileLoader'];
        }
        
        // pour chaque environnement déclaré dans la configuration initialisation des instances
        foreach ($environments as $environment) {
            /** @var Environment $environment */
            // dump($appKey);die;
            // @TODO sans doute besoin d'un instance builder pour redécouper qui prendrait les configs urlHanlder et AuthHandler
            $this->initInstances($environment, $urlHandlersConfig, $authHandlerConfig, $releaseFileOverwriteConfig, $releaseFileLoaderConfig);
        }
    }
    
    /**
     * Check application configuration.
     * Check This sections :
     * - URL Handler
     */
    private function CheckApplicationConfiguration($applicationConfig)
    {
        $this->checkURLHandlerApplicationConfiguration($applicationConfig['URLHandler']);
    }
    
    /**
     * Check URL Handler application configuration consistency.
     * Check this point :
     * - Check if all URl Handler env defined (except default) in the application exist in environment list, if not, an exception is thrown
     * - check if all declared URL Handler for this application.
     *
     * @throws InvalidConfigurationException if the URLHandler env declared in application doesn't exist.
     */
    private function checkURLHandlerApplicationConfiguration($config)
    {
        foreach ($config as $envKey => $urlHandlerConfig) {
            
            if ($envKey != 'default' && ! array_key_exists($envKey, $this->environments)) {
                
                throw new InvalidConfigurationException('The URLHandler configuration of  "' . $this->appName . '" application is wrong.Environment "' . $envKey . '" used is not defined in environment list ! Available Environment ' . implode(', ', array_keys($this->environments)));
            }
            
            if (! array_key_exists($urlHandlerConfig['handler'], $this->urlHandlersConfigs)) {
                throw new InvalidConfigurationException('The URLHandler "' . $urlHandlerConfig['handler'] . '" used in configuration of "' . $this->appName . '" application was not declared. Available URLHandler : ' . implode(', ', array_keys($this->urlHandlersConfigs)) . '.');
            }
        }
    }
    
    /**
     * initialise les différentes instances de l'application.
     * instancie le bon URL handler selon l'environnemnt déclaré dans la configuration
     * Si aucune déclaration n'est déclaré dans aucun des handlers l'instance n'est pas créé
     *
     * @todo ça commence à être compliqué tout ça ... need refactoring => applicationInstanceBuilder
     *
     *
     * @todo pas top, ce n'est pas cette classe qui devrait savoir si elle doit instancier l'application pour cet envrionnement
     *
     * @param Environment $environment
     *            environnement pour lequel doit être instancié l'application
     * @param $urlHandlersConfig configurations
     *            URLHandlers (TemplatedHostURLHandler, ...)
     * @todo $urlHandlersConfig ne devrait pas contenir toutes les configuraitons de tous les URLhandlers
     *       comme c'est le cas actuellement, ici on devrait savoir lequel il faut car on connais le type d'application.
     *       Cela doit se passer dans la classe qui instancie cette classe.
     * @param
     *            array[] | null $authHandlerConfig authenticationHandler configuration, null if none specified
     *
     * @param $array[string] $releaseFileOverwriteConfig
     *            configuration overwrite for this application
     * @param $array[string] $releaseFileLoaderConfig
     *            configuration for the releaseFileLoader
     */
    private function initInstances(Environment $environment, $urlHandlersConfig, $authHandlerConfig, $releaseFileOverwriteConfig, $releaseFileLoaderConfig)
    {
        $urlHandler = $this->initURLHandler($environment, $urlHandlersConfig);
        
        if (! $urlHandler) {
            // pas de handler pour cet environnement => pas d'instanciation pour cet envrionnement.
            return false;
        }
        
        $authHandler = $this->initAuthenticationHandler($environment, $authHandlerConfig);
        
        $releaseFileLoaderBuilder = new ReleaseFileLoaderBuilder($this->appType, $environment);
        $releaseFileLoader = $releaseFileLoaderBuilder->build();
        
        if ($releaseFileLoaderConfig != null) {
            $releaseFileLoader->handleConfiguration($releaseFileLoaderConfig);
        }
        
        if ($authHandler) {
            $releaseFileLoader->setAuthenticationHandler($authHandler);
        }
        
        $rfFactory = ReleaseFileFactory::getInstance();
        // releaseFile creation based on file type specified by the application type.
        $releaseFile = $rfFactory::getReleaseFile($this->appType, $environment);
        
        $releaseFileLoader->setReleaseFile($releaseFile);
        
        $releaseFile->setLoader($releaseFileLoader);
        
        $releaseFile->handleConfiguration($this->appType, $releaseFileOverwriteConfig);
        
        $appInstance = new AppInstance($environment, $urlHandler, $releaseFile, $this->appType);
        
        // pas sur que ce soit nécessaire de stocker dans un tableau puisque c'est le context qui est passé
        // et qui maintien sa propre liste d'application
        $this->appInstances[$environment->getTrigram()] = $appInstance;
        
        // dump($appInstance);
    }
    
    /**
     * init an Authentication handler if needed
     *
     * @param Environment $environment
     *            environment in instanciation
     * @param array[] $urlHandlersConfig
     *            Configurations for all UrlHandler
     *
     * @return AuthenticationHandler
     */
    private function initAuthenticationHandler(Environment $environment, $authHandlerConfig)
    {
        
        // dump($environment);dump($authHandlerConfig);
        $ahFactory = AuthenticationHandlerFactory::getInstance();
        // is an authentication is defined for this environment ?
        if (isset($authHandlerConfig[$environment->getTrigram()])) {
            $authenticationHandler = $ahFactory::getAuthenticationHandler($authHandlerConfig[$environment->getTrigram()]);
        } // or a default configuration
        elseif (isset($authHandlerConfig['default'])) {
            $authenticationHandler = $ahFactory::getAuthenticationHandler($authHandlerConfig['default']);
        } else {
            return false;
        }
        
        // dump($authenticationHandler);
        
        return $authenticationHandler;
    }
    
    /**
     * initialize the URLHandler for the environment in param
     *
     * @param Environment $environment
     *            environment in instanciation
     * @param array[] $urlHandlersConfig
     *            Configurations for all UrlHandler
     *
     * @return URLHandler | boolean return the instanciated URLHandler or false if no instanciation needed (no configuration)
     *
     * @todo rename form initURLHandler to initHostHandler
     */
    private function initURLHandler(Environment $environment, $urlHandlersConfig)
    {
        $factory = URLHandlerFactory::getInstance();
        
        // check if a specific configuration is defined in the instance application is defined.
        if (isset($this->urlHandler[$environment->getTrigram()])) {
            
            $urlHandler = $factory::getURLHandler($urlHandlersConfig[$this->urlHandler[$environment->getTrigram()]['handler']]['type'],
                $urlHandlersConfig[$this->urlHandler[$environment->getTrigram()]['handler']]);
            $urlHandler->setHost($this->urlHandler[$environment->getTrigram()]['appHost']);
            // set URL Scheme
            if (isset($this->urlHandler[$environment->getTrigram()]['https'])) {
                $urlHandler->isHttps = $this->urlHandler[$environment->getTrigram()]['https'];
            }
        } // otherwise take the default handler defined in the configuration
        elseif (isset($this->urlHandler['default']) && isset($urlHandlersConfig[$this->urlHandler['default']['handler']]['envHosts'][$environment->getTrigram()])) {
            $urlHandler = $factory::getURLHandler($urlHandlersConfig[$this->urlHandler['default']['handler']]['type'], $urlHandlersConfig[$this->urlHandler['default']['handler']]);
            $urlHandler->setHost($this->urlHandler['default']['appHost']);
            // set URL Scheme
            if (isset($this->urlHandler['default']['https'])) {
                $urlHandler->isHttps = $this->urlHandler['default']['https'];
            }
        } // pas d'instanciation pour cet environnement nécessaire car pas de handler qui gère cette config.
        else {
            return false;
        }
        
        return $urlHandler;
    }
    
    /**
     * validate version against each others
     *
     * @called by controler
     *
     * @see VersionValidator method check equals
     * @todo refacto : this method should be called "control" or "validate". Version validation should be masked
     */
    abstract public function validateVersion();
    
    /**
     * Load application information against instance
     * return all instances if environment is ommited
     *
     * @param string $environment
     *            : environment id
     *
     * @return array : instance with their informations
     */
    public function loadVersion($environment = null)
    {
        
        // si environment n'est pas défini on charge toutes les instances
        if (null === $environment) {
            
            foreach ($this->appInstances as $appInstance) {
                // if instance is valid
                if (false !== $appInstance->loadVersion()) {
                    $this->versionManager->manageInstance($appInstance);
                }
            }
            return $this;
        } else {
            if (! isset($this->appInstances[$environment])) {
                throw new \UnexpectedValueException("Environnement '$environment' not configured for the " . $this->getName() . " application");
            }
            return $this->getAppInstance($environment)->loadVersion();
        }
    }
    
    /**
     * return instance according environment requested or all instance if environment ommited
     *
     * @param string $env
     *            env id for the requested instance
     *
     * @return AppInstance|array[AppInstance] requested instance or all instance if $env is ommited.
     */
    function getAppInstance($env = null)
    {
        if ($env) {
            if (isset($this->appInstances[$env])) {
                return $this->appInstances[$env];
            } else {
                return false;
            }
        }
        return $this->appInstances;
    }
    
    /**
     * retourne le plus ancien tag de toutes les instances
     *
     * @deprecated n'est plus utilisé ou doit être déplacé ailleurs
     * @return AppInstance
     */
    function getOldestReleaseTag()
    {
        return $this->versionManager->getOldestReleaseTag();
    }
    
    function getName()
    {
        return $this->appName;
    }
    
    function getHost()
    {
        // @TODO il faut gérer ce point le host n'est plus rempli au niveau
        // de l'application il devrait prendre la clé de l'applicaiton dans la config à la place
        // et voir les impacts => je ne vois plus ce que je voulais dire ???
        return $this->appHost;
    }
    
    /**
     * fonction de tri des Applications selon leur nom
     *
     * @param Application $a
     * @param Application $b
     */
    static function sortApplicationByName($a, $b)
    {
        return strcmp($a->getName(), $b->getName());
    }
    
    /**
     * retroune l'objet de validation des versions
     *
     * @return
     *
     */
    public function getVersionValidator()
    {
        return $this->versionValidator;
    }
    
    /**
     * Set le validateur de version
     *
     * @param VersionValidator $versionValidator
     */
    public function setVersionValidator(VersionValidator $versionValidator)
    {
        $this->versionValidator = $versionValidator;
    }
    
    public function getAppKey()
    {
        return $this->appKey;
    }
    
    public function setAppKey($appKey)
    {
        $this->appKey = $appKey;
    }
    
    /**
     *
     * @return the application type
     */
    public function getAppType()
    {
        return $this->appType;
    }
}
