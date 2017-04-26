<?php
use Bbr\VersionViewerBundle\Applications\AppInstance;
use Bbr\VersionViewerBundle\Applications\ApplicationType\ApplicationType;
use Bbr\VersionViewerBundle\Applications\ColorManager;
use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\ReleaseFileConfiguration;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\JsonReleaseFile;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile;
use Bbr\VersionViewerBundle\Applications\URLHandler\FullHostURLHandler;
use Bbr\VersionViewerBundle\Applications\ApplicationType\DefaultApplication;
use Bbr\VersionViewerBundle\Applications\URLHandler\TemplatedHostURLHandler;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 *
 * @author bbonnesoeur
 *        
 *         Implement test of the color manager Class
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    private $applicationConfig;

    private $appKey;

    private $environments;

    private $urlHandlersConfig;

    private $appType;

    public function setUp()
    {
        parent::setUp();
        
        $this->applicationConfig = array(
            'URLHandler' => array(
                'default' => array(
                    'handler' => 'MyURLHandler',
                    'appHost' => 'myhost'
                ),
                'cus' => array(
                    'handler' => 'MyURLHandler',
                    'appHost' => 'myotherhost'
                ),
                'prod' => array(
                    'handler' => 'MyURLHandlerProd',
                    'appHost' => 'myotheronehost'
                )
            ),
            'appName' => 'my fantastic app',
            'apptype' => 'myapptype'
        );
        
        $this->appKey = "myAppKey";
        
        $appTypeConfig = array(
            "id" => "myapptype",
            "name" => "My Application type",
            "releaseFilePath" => "/release.txt",
            "fileType" => "text",
            "filteredProperties" => array(
                "version" => "Release tag :(.*)",
                "release date" => "Release date :(.*)"
            ),
            "comparisonValue" => "version"
        );
        
        $this->appType = new ApplicationType($appTypeConfig);
        
        $this->environments = array(
            "dev" => new Environment(array(
                'name' => 'development',
                'trigram' => 'dev'
            )),
            "prod" => new Environment(array(
                'name' => 'production',
                'trigram' => 'prod'
            ))
        );
        
        $this->urlHandlersConfig = array(
            'FullHostURLHandler' => null,
            'MyURLHandler' => array(
                'type' => 'TemplatedHostURLHandler',
                'envhosts' => array(
                    'dev' => 'dev'
                )
            )
        );
    }

    /**
     * Test if an exception is thrown if an URL handler declare a configuraiton for a not defined environment.
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessaThe URLHandler configuration of  "my fantastic app" application is wrong.Environment "cus" used is not defined in environment list ! Available Environment dev, prodge 
     */
    public function testcheckURlHandlerConfigurationAgainstAvailableEnvironment()
    {
        $this->application = new DefaultApplication($this->applicationConfig, $this->appKey, $this->environments, $this->urlHandlersConfig, $this->appType);
    }

    /**
     * Test if an excpetion is thrown if an URLHandler used in applicaiton configuraiton is not declared
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The URLHandler "MyURLHandlerProd" used in configuration of "my fantastic app" application was not declared. Available URLHandler : FullHostURLHandler, MyURLHandler.
     */
    public function testCheckURlHandlerConfigurationUsedByApplication()
    {
        $this->applicationConfig = array(
            'URLHandler' => array(
                'default' => array(
                    'handler' => 'MyURLHandler',
                    'appHost' => 'myhost'
                ),
                'prod' => array(
                    'handler' => 'MyURLHandlerProd',
                    'appHost' => 'myotheronehost'
                )
            ),
            'appName' => 'my fantastic app',
            'apptype' => 'myapptype'
        );
        
        $this->application = new DefaultApplication($this->applicationConfig, $this->appKey, $this->environments, $this->urlHandlersConfig, $this->appType);
    }
}