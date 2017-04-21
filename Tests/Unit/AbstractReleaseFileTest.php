<?php
use Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\TextReleaseFile;
use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\ReleaseFileConfiguration;
use Bbr\VersionViewerBundle\Applications\ApplicationType\ApplicationType;

/**
 *
 * @author bbonnesoeur
 *        
 *         Implement test on method defined in the AbstractReleaseFile Class.
 */
class AbstractReleaseFileTest extends \PHPUnit_Framework_TestCase
{

    private $env;

    private $appType;

    private $releaseFile;

    public function setUp()
    {
        parent::setUp();
        
        $this->env = new Environment(array(
            'name' => 'customer',
            'trigram' => 'cus'
        ));
        
        $this->releaseFile = new TextReleaseFile($this->env, new ReleaseFileConfiguration());
        
        $this->appType = new ApplicationType(array(
            'id' => 'myapptype',
            'name' => 'My app type',
            'releaseFilePath' => 'app/type/file/path.txt',
            'fileType' => 'txt',
            'filteredProperties' => array(
                'firstPropertie' => 'first/propertie',
                'secondPropertie' => 'second/propertie',
                'thirdPropertie' => 'third/propertie',
                'forthPropertie' => 'forth/propertie'
            )
        ));
    }

    /**
     * test how is handled all configuration possibilities for filtered properties which
     * can be defined at application type level and overidden at application level
     */
    public function testFilteredProperties()
    {
        // only appType Configuration
        $this->releaseFile->handleConfiguration($this->appType);
        $this->assertTrue($this->releaseFile->getConfiguration()
            ->getFilteredProperties() == $this->appType->getFilteredProperties());
        
        // appType Configuration overrided fully by a complete default configuration 
        $overrideFilteredProperties = array(
            'default' => array(
                'filteredProperties' => array(
                    'firstPropertie' => 'first/overriden/propertie',
                    'secondPropertie' => 'second/overriden/propertie'
                )
            )
        );
        
        $expectedArray = array (
            'firstPropertie' => 'first/overriden/propertie',
            'secondPropertie' => 'second/overriden/propertie',
            'thirdPropertie' => 'third/propertie',
            'forthPropertie' => 'forth/propertie'
        );
        
        $this->releaseFile->handleConfiguration($this->appType, $overrideFilteredProperties);
        
        $this->assertTrue($this->releaseFile->getConfiguration()
            ->getFilteredProperties() == $expectedArray);
        
        // apptype Configuration overrided by an environment specific override and remove the third property
        $overrideFilteredProperties = array(
            'cus' => array(
                'filteredProperties' => array(
                    'firstPropertie' => 'first/overriden/propertie',
                    'secondPropertie' => 'second/overriden/propertie',
                    'thirdPropertie' => null //at thi step the '~' in configuraiton was converted as 'null' by SF configuraiton manager
                )
            )
        );
        
        $expectedArray = array (
            'firstPropertie' => 'first/overriden/propertie',
            'secondPropertie' => 'second/overriden/propertie',
            'forthPropertie' => 'forth/propertie'
        );
        
//         var_dump($this->appType);
//         echo '\n';
//         var_dump($overrideFilteredProperties);
        
        $this->releaseFile->handleConfiguration($this->appType, $overrideFilteredProperties);
        
        $this->assertTrue($this->releaseFile->getConfiguration()
            ->getFilteredProperties() == $expectedArray);
        
        // apptype configuration overrided by an environment specific override in spite of a defaiult override
        $overrideFilteredProperties = array(
            'default' => array(
                'filteredProperties' => array(
                    'firstPropertie' => 'first/overriden/default/propertie',
                    'secondPropertie' => 'second/overriden/default/propertie'
                )
            ),
            'cus' => array(
                'filteredProperties' => array(
                    'firstPropertie' => 'first/overriden/cus/propertie',
                    'secondPropertie' => 'second/overriden/cus/propertie',
                    'thirdPropertie' => 'third/cus/propertie'
                )
            )
        );
        
        $expectedArray = array (
            'firstPropertie' => 'first/overriden/cus/propertie',
            'secondPropertie' => 'second/overriden/cus/propertie',
            'thirdPropertie' => 'third/cus/propertie',
            'forthPropertie' => 'forth/propertie'
        );
        
        $this->releaseFile->handleConfiguration($this->appType, $overrideFilteredProperties);
        $this->assertTrue($this->releaseFile->getConfiguration()
            ->getFilteredProperties() == $expectedArray);
        
        // apptype configuration not overrided by a non related environment specific override.
        $overrideFilteredProperties = array(
            'prod' => array(
                'filteredProperties' => array(
                    'firstPropertie' => 'first/overriden/propertie',
                    'secondPropertie' => 'second/overriden/propertie'
                )
            )
        );
        
        $this->releaseFile->handleConfiguration($this->appType, $overrideFilteredProperties);
        $this->assertTrue($this->releaseFile->getConfiguration()
            ->getFilteredProperties() == $this->appType->getFilteredProperties());
    }

    /**
     * test how is handled all configuration possibilities for release file path which
     * can be defined at application type level and overidden at application level
     */
    public function testHandleFilePath()
    {
        
        // only appType Configuration
        $this->releaseFile->handleConfiguration($this->appType);
        $this->assertEquals($this->releaseFile->getFilePath(), $this->appType->getFilePath());
        
        // appType Configuration overrided fully by a complete default configuration
        $overrideFilePathConfiguration = array(
            'default' => array(
                'releaseFilePath' => 'default/path/override.txt'
            )
        );
        
        $this->releaseFile->handleConfiguration($this->appType, $overrideFilePathConfiguration);
        $this->assertEquals($this->releaseFile->getFilePath(), $overrideFilePathConfiguration['default']['releaseFilePath']);
        
        // apptype Configuration overrided by an environment specific override
        $overrideFilePathConfiguration = array(
            'cus' => array(
                'releaseFilePath' => 'customer/path/override.txt'
            ),
            'prod' => array(
                'releaseFilePath' => 'prod/path/override.txt'
            )
        );
        $this->releaseFile->handleConfiguration($this->appType, $overrideFilePathConfiguration);
        $this->assertEquals($this->releaseFile->getFilePath(), $overrideFilePathConfiguration['cus']['releaseFilePath']);
        
        // apptype configuration overrided by an environment specific override in spite of a defaiult override
        $overrideFilePathConfiguration = array(
            'default' => array(
                'releaseFilePath' => 'default/path/override.txt'
            ),
            'cus' => array(
                'releaseFilePath' => 'cus/path/override.txt'
            )
        );
        $this->releaseFile->handleConfiguration($this->appType, $overrideFilePathConfiguration);
        $this->assertEquals($this->releaseFile->getFilePath(), $overrideFilePathConfiguration['cus']['releaseFilePath']);
        
        // apptype configuration not overrided by a non related environment specific override.
        $overrideFilePathConfiguration = array(
            'prod' => array(
                'releaseFilePath' => 'cus/path/override.txt'
            )
        );
        $this->releaseFile->handleConfiguration($this->appType, $overrideFilePathConfiguration);
        $this->assertEquals($this->releaseFile->getFilePath(), $this->appType->getFilePath());
    }
}