<?php
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\HttpReleaseFileLoader;
use Bbr\VersionViewerBundle\Applications\Environment;

/**
 *
 * @author bbonnesoeur
 *         implements test on the ReleaseFileLoader
 *        
 */
class HttpReleaseFileLoaderTest extends \PHPUnit_Framework_TestCase
{

    private $env;

    private $releaseFileLoader;

    /**
     * Set up an HttpReleaseFileLoader with a Customer (cus) environmment
     */
    public function setup()
    {
        parent::setUp();
        
        $this->env = new Environment(array(
            'name' => 'customer',
            'trigram' => 'cus'
        ));
        
        $this->releaseFileLoader = new HttpReleaseFileLoader($this->env);
    }

    /**
     * test the timeout configuration handling.
     */
    public function testTimeoutConfiguration()
    {
        
        // Timeout configured by default value (ie not specified in override config)
        $configuration = array();
        
        $expectedContextOptions = array(
            'http' => array(
                'timeout' => HttpReleaseFileLoader::DEFAULT_TIME_OUT,
                'ignore_errors' => true
            )
        );
        
        $this->releaseFileLoader->handleConfiguration($configuration);
        
        // var_dump($this->releaseFileLoader->getContextOptions());
        // die;
        
        $this->assertTrue($this->releaseFileLoader->getContextOptions() == $expectedContextOptions);
        
        // Timeout configured in the override section by default
        $configuration = array(
            'default' => array(
                'timeout' => 5
            )
        );
        
        $expectedContextOptions = array(
            'http' => array(
                'timeout' => 5,
                'ignore_errors' => true
            )
        );
        
        $this->releaseFileLoader->handleConfiguration($configuration);
        
        $this->assertTrue($this->releaseFileLoader->getContextOptions() == $expectedContextOptions);
        
        // Configuraiton overrided but not for current environment
        $configuration = array(
            'int' => array(
                'timeout' => 7
            )
        );
        
        // 5 expected as seted in precendent test case.
        $expectedContextOptions = array(
            'http' => array(
                'timeout' => 5,
                'ignore_errors' => true
            )
        );
        
        $this->releaseFileLoader->handleConfiguration($configuration);
        
        $this->assertTrue($this->releaseFileLoader->getContextOptions() == $expectedContextOptions);
        
        // configuration overrided for the current environment although there is a default configuraiton
        $configuration = array(
            'default' => array(
                'timeout' => 3
            ),
            'cus' => array(
                'timeout' => 8
            )
        );
        
        // 5 expected as seted in precendent test case.
        $expectedContextOptions = array(
            'http' => array(
                'timeout' => 8,
                'ignore_errors' => true
            )
        );
        
        $this->releaseFileLoader->handleConfiguration($configuration);
        
        $this->assertTrue($this->releaseFileLoader->getContextOptions() == $expectedContextOptions);
    }

    /**
     * test the context option method
     */
    public function testsetHttpContextOption()
    {
        
        // test simple option
        $expectedContextOptions = array(
            'http' => array(
                'timeout' => 5,
                'ignore_errors' => true
            )
        );
        
        $this->releaseFileLoader->setHttpContextOption('timeout', 5);
        
        $this->assertTrue($this->releaseFileLoader->getContextOptions() == $expectedContextOptions);
        
        // test adding more than one option to header
        $expectedContextOptions = array(
            'http' => array(
                'header' => "toto\r\ntiti",
                'timeout' => 5,
                'ignore_errors' => true
            )
        );
        $this->releaseFileLoader->setHttpContextOption('header', 'toto');
        $this->releaseFileLoader->setHttpContextOption('header', 'titi');
        
        $this->assertTrue($this->releaseFileLoader->getContextOptions() == $expectedContextOptions);
    }

    /**
     * test the Url scheme configuration handling method through get URLScheme method.
     */
    public function testUrlSchemeConfiguration()
    {
        // https configured not for not relevent env
        $configuration = array(
            'int' => array(
                'https' => true
            )
        );
        
        $this->releaseFileLoader->handleConfiguration($configuration);
        $this->assertTrue($this->releaseFileLoader->getHttps() == false);
        
        // https configured to true for all env
        $configuration = array(
            'default' => array(
                'https' => true
            )
        );
        
        $this->releaseFileLoader->handleConfiguration($configuration);
        $this->assertTrue($this->releaseFileLoader->getHttps() == true);
        
        // https configured to false for cus env
        $configuration = array(
            'cus' => array(
                'https' => false
            )
        );
        
        $this->releaseFileLoader->handleConfiguration($configuration);
        $this->assertTrue($this->releaseFileLoader->getHttps() == false);
        
        // https configured to true for cus env,  default set to false
        $configuration = array(
            'default' => array(
                'https' => false
            ),
            'cus' => array(
                'https' => true
            )
        );
        
        $this->releaseFileLoader->handleConfiguration($configuration);
        $this->assertTrue($this->releaseFileLoader->getHttps() == true);
    }
}