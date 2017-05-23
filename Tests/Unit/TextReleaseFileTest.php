<?php
use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\JsonPropertieTransformer;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\ReleaseFileConfiguration;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\JsonReleaseFile;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\TextReleaseFile;
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\HttpReleaseFileLoader;
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\LocalReleaseFileLoader;

/**
 *
 * @author bbonnesoeur
 *
 *         Implement test on method defined in the AbstractReleaseFile Class.
 */
class TextReleaseFileTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     *
     * @var Environment
     */
    private $env;
    
    /**
     *
     * @var ReleaseFileConfiguration
     */
    private $releaseFileConfiguration;
    
    /**
     *
     * @var JsonReleaseFile
     */
    private $textReleaseFile;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->env = new Environment(array(
            'name' => 'customer',
            'trigram' => 'cus'
        ));
        
        $this->releaseFileConfiguration = new ReleaseFileConfiguration();
        
        $this->textReleaseFile = new TextReleaseFile($this->env, $this->releaseFileConfiguration);
        $this->textReleaseFile->setLoader(new LocalReleaseFileLoader(__DIR__ . '/../Fixtures/textReleaseFile.txt'));
    }
    
    /**
     * Test propertie at root level and at object level
     */
    public function testFilteredDataFoundPropertie()
    {
        $this->releaseFileConfiguration->setFilteredProperties(array(
            'version' => 'Release tag :(.*)',
            'release date' => 'Release date :(.*)'
        ));
        
        $this->textReleaseFile->load();
        
        $expectedResult = json_encode(array(
            'version' => ' release-20160629-1348',
            'release date' => ' June 29 2016'
        ));
        
        
        $this->assertTrue($expectedResult == $this->textReleaseFile->getPropertiesJson());
    }
    
    /**
     * Test that unfound properties are wall handled
     * 
     */
    public function testFilteredDataNotFoundPropertie()
    {
        $this->releaseFileConfiguration->setFilteredProperties(array(
            'version' => 'Release tag :(.*)',
            'unfound Property' => 'my not found pattern :(.*)'
        ));
        
        $this->textReleaseFile->load();
        
        $expectedResult = json_encode(array(
            'version' => ' release-20160629-1348',
            'unfound Property' => 'not found !'
        ));
        
        $this->assertTrue($expectedResult == $this->textReleaseFile->getPropertiesJson());
    }
    
    /**
     * Test if filtered data had only two warning if two properties are not found
     * 
     */
    public function testFilteredDataObjectHasWarning(){
        $this->releaseFileConfiguration->setFilteredProperties(array(
            'version' => 'not in the release file (.*)',
            'unfound Property' => 'not in the release file (.*)'
            
        ));
        $this->textReleaseFile->load();
        
        $expectedResult = json_encode(array(
            'version' => 'not found !',
            'unfound Property' => 'not found !'
        ));

        $this->assertTrue($expectedResult == $this->textReleaseFile->getPropertiesJson());
        $this->assertEquals(2, count($this->textReleaseFile->getWarnings()));
    }
    
}