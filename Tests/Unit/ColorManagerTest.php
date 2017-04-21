<?php
use Bbr\VersionViewerBundle\Applications\AppInstance;
use Bbr\VersionViewerBundle\Applications\ApplicationType\ApplicationType;
use Bbr\VersionViewerBundle\Applications\ColorManager;
use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\ReleaseFileConfiguration;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\JsonReleaseFile;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile;
use Bbr\VersionViewerBundle\Applications\URLHandler\FullHostURLHandler;

/**
 *
 * @author bbonnesoeur
 *        
 *         Implement test of the color manager Class
 */
class ColorManagerTest extends \PHPUnit_Framework_TestCase
{

    private $appInstance;

    private $colorManager;

    public function setUp()
    {
        parent::setUp();
        
        $this->colorManager = new ColorManager();
        
        $environment = new Environment(array(
            'trigram' => 'dev',
            'name' => 'development'
        ));
        $releaseFileConfig = new ReleaseFileConfiguration();
        $releaseFileConfig->setComparisonValue('version');
        
        $releaseFile = new JsonReleaseFile($environment, $releaseFileConfig);
        $releaseFile->setProperties(array(
            'version' => '1.5'
        ));
        $this->appInstance = new AppInstance($environment, new FullHostURLHandler(), $releaseFile, new ApplicationType(array(
            'id' => 'app',
            'name' => 'my app',
            'releaseFilePath' => '/path/to/file.json',
            'fileType' => 'json'
        )));
    }

    /**
     * test if an unknow Version of an instance as a different version than affaected before
     */
    public function testAttributeColorForAnUnknownVersion()
    {
        $colorManager = new ColorManager();
        $colorManager->releaseColors = array(
            '2.5' => 'label-success'
        );
        $colorManager->colors['label-success'] = true;
        
        $colorManager->attributeColor($this->appInstance);
        
        $this->assertTrue($this->appInstance->getColor() != 'label-success');
    }

    /**
     * test if an instance of a known version as the same attributed color
     */
    public function testAttributeColorForAKnownVersion()
    {
        $colorManager = new ColorManager();
        $colorManager->releaseColors = array(
            '1.5' => 'label-success'
        );
        $colorManager->colors['label-success'] = true;
        
        $colorManager->attributeColor($this->appInstance);
        $this->assertTrue($this->appInstance->getColor() == 'label-success');
    }
}