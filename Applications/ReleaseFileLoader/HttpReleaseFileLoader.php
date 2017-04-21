<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFileLoader;

use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\XmlReleaseFile;

/**
 * Loader over Http request
 *
 * @author bbonnesoeur
 *        
 */
class HttpReleaseFileLoader extends AbstractReleaseFileLoader
{

    /**
     *
     * @var call context
     */
    private $context;

    /**
     *
     * @var array[array]context options
     */
    private $contextOptions;

    /**
     *
     * @var AuthenticationHandler authentication Handler
     */
    private $authenticationHandler;

    /**
     *
     * @var string $url whitout scheme to release file to load (ie: mydomain/mypath/to/releasefile.ext)
     */
    private $url;

    /**
     * indicate URL scheme to use
     */
    private $https = false;

    /**
     *
     * @var integer default timeout value used
     */
    const DEFAULT_TIME_OUT = 2;

    /**
     * init loader with default timeout
     *
     * @see HttpReleaseFileLoader::DEFAULT_TIME_OUT
     *
     * @param Environment $environment
     *            environment for this loader.
     */
    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        
        $this->contextOptions = array(
            'http' => array(
                'timeout' => HttpReleaseFileLoader::DEFAULT_TIME_OUT,
                'ignore_errors' => true
            )
        );
        
        return $this;
    }

    /**
     * Load File content accordingly context
     *
     * @param ReleaseFile $releaseFile
     *            the releaseFile to load
     *            
     * @throws ReleaseFileLoadingException
     *
     * @return string | boolean releaseFile content or false if an error occured
     */
    public function load()
    {
        $this->url = $this->buildUrl();
        
        $this->authenticate();
        $this->handleAcceptHeader();
        $this->initContext();
        
        $content = @file_get_contents($this->url, 0, $this->context);
        if (! $content) {
            throw new ReleaseFileLoadingException("HTTP request failed. Error !! ");
            return false;
        }
        
        return $content;
    }

    /**
     * return URL scheme
     *
     * @return string http:// or https:// according https member value
     */
    private function getUrlScheme()
    {
        if ($this->https) {
            return 'https://';
        }
        return 'http://';
    }

    /**
     * authenticate through actual authentication handler if exist
     */
    private function authenticate()
    {
        if ($this->authenticationHandler != null)
            $this->authenticationHandler->authenticate($this);
    }

    /**
     * Set an Http option on the call context used during file loading
     *
     * @todo maybe need to be more robust for header handling.
     *      
     * @param string $key
     *            option name
     * @param string $value
     *            option value
     */
    public function setHttpContextOption($key, $value)
    {
        // for header value can be multiplpe (header :Authorization + Accept for example)
        // \r\n will be added to the first value and concatenated with new one.
        // Warning : this does not allow override of a value !!
        if ($key == 'header' && isset($this->contextOptions['http'][$key])) {
            $this->contextOptions['http'][$key] = $this->contextOptions['http'][$key] . "\r\n" . $value;
        } else {
            $this->contextOptions['http'][$key] = $value;
        }
    }

    private function initContext()
    {
        $this->context = stream_context_create($this->contextOptions);
    }

    /**
     * set the authentication Handler
     */
    public function setAuthenticationHandler($authenticationHandler)
    {
        $this->authenticationHandler = $authenticationHandler;
    }

    /**
     * add parameters to Url
     *
     * @param string $parameters            
     */
    public function addUrlParam($parameters)
    {
        $this->url = $this->url . $parameters;
    }

    /**
     *
     * @return string url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set Accept header according ressource type configured
     * Only set xml header at the moment.
     * Accept header string set here handle majority of bad server.
     */
    private function handleAcceptHeader()
    {
        if ($this->releaseFile instanceof XmlReleaseFile) {
            $this->setHttpContextOption("header", "Accept:text/html,application/xhtml+xml,application/xml,text/xml");
        }
    }

    /**
     * handle configuration for releaseFileLoader in the ReleaseFIleOverwrite section
     * At the moment handle only timeout parameter
     *
     * @param array[string] $configuration            
     *
     * @todo REFACTO CONFIG : this config must be moved in ReleaseFileLoaderConfiguration configuration section.
     */
    public function handleConfiguration($configuration)
    {
        $this->handleTimeoutConfiguration($configuration);
        $this->handleUrlSchemeConfiguration($configuration);
    }

    /**
     * handle URL scheme configuration (http or https) according environment
     *
     * @param array[string] $configuration
     *            configuration at application instance level
     *            
     * @return \Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\HttpReleaseFileLoader
     */
    private function handleUrlSchemeConfiguration($configuration)
    {
        // is a specific configuration is defined for this environment ?
        if (isset($configuration[$this->environment->getTrigram()])) {
            if (isset($configuration[$this->environment->getTrigram()]['https'])) {
                $this->https = $configuration[$this->environment->getTrigram()]['https'];
            }
        } // or a default configuration
elseif (isset($configuration['default'])) {
            if (isset($configuration['default']['https'])) {
                $this->https = $configuration['default']['https'];
            }
        }
        return $this;
    }

    /**
     * Handle time out configuration and configure releaseFileLoader according configured value and overwrite.
     *
     * if none configuration declared, default time out is not modified
     *
     * @see HttpReleaseFileLoader:DEFAULT_TIME_OUT
     *
     * @param array[string] $configuration
     *            configuraiton at the application instance level (ie overide of the application type)
     */
    private function handleTimeoutConfiguration($configuration = NULL)
    {
        if (is_null($configuration)) {
            return $this;
        }
        
        if (isset($configuration['default']) && isset($configuration['default']['timeout'])) {
            $this->setHttpContextOption('timeout', $configuration['default']['timeout']);
        }
        
        if (isset($configuration[$this->environment->getTrigram()]) && isset($configuration[$this->environment->getTrigram()]['timeout'])) {
            $this->setHttpContextOption('timeout', $configuration[$this->environment->getTrigram()]['timeout']);
        }
        
        return $this;
    }

    /**
     * build the complete URL of the release FIle
     *
     * @return string URL (ie : http://mydomain.com/mypath/file.ext
     */
    private function buildUrl()
    {
        return $this->getUrlScheme() . $this->releaseFile->getHost() . $this->releaseFile->getFilePath();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\AbstractReleaseFileLoader::getRessourceCommand()
     */
    public function getRessourceCommand()
    {
        return $this->buildUrl();
    }

    /**
     *
     * @return the array[array]context
     */
    public function getContextOptions()
    {
        return $this->contextOptions;
    }

    /**
     *
     * @return the Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     *
     * @return the unknown_type
     */
    public function getHttps()
    {
        return $this->https;
    }
}