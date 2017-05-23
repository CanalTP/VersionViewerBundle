<?php
use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\ReleaseFileConfiguration;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\XmlReleaseFile;
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\LocalReleaseFileLoader;

/**
 *
 * @author bbonnesoeur
 *        
 *         Implement test on method defined in the AbstractReleaseFile Class.
 */
class XmlReleaseFileTest extends \PHPUnit_Framework_TestCase
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
     * @var XMLReleaseFile
     */
    private $xmlReleaseFile;

    public function setUp()
    {
        parent::setUp();
        
        $this->env = new Environment(array(
            'name' => 'customer',
            'trigram' => 'cus'
        ));
        
        $this->releaseFileConfiguration = new ReleaseFileConfiguration();
        
        $this->xmlReleaseFile = new XmlReleaseFile($this->env, $this->releaseFileConfiguration);
        $this->xmlReleaseFile->setLoader(new LocalReleaseFileLoader(__DIR__ . '/../Fixtures/xmlReleaseFile.xml'));
    }

    /**
     * Test propertie at root level and at object level
     */
    public function testFilteredDataFoundPropertie()
    {
        $this->releaseFileConfiguration->setFilteredProperties(array(
            'version' => '//Version',
            'release date' => '//Version/@deployed_at'
        ));
        
        $this->xmlReleaseFile->load();
        
        $expectedResult = json_encode(array(
            'version' => '1.5.5',
            'release date' => '2017-04-12T16:18:54'
        ));
        
        $this->assertTrue($expectedResult == $this->xmlReleaseFile->getPropertiesJson());
    }

    /**
     * Test that unfound properties are wall handled
     */
    public function testFilteredDataNotFoundPropertie()
    {
        $this->releaseFileConfiguration->setFilteredProperties(array(
            'version' => '//Version',
            'unfound Property' => '//mynotfoundxpath'
        ));
        
        $this->xmlReleaseFile->load();
        
        $expectedResult = json_encode(array(
            'version' => '1.5.5',
            'unfound Property' => 'not found !'
        ));
        
        $this->assertTrue($expectedResult == $this->xmlReleaseFile->getPropertiesJson());
    }

    /**
     * Test if filtered data had only two warning if two properties are not found
     */
    public function testFilteredDataObjectHasWarning()
    {
        $this->releaseFileConfiguration->setFilteredProperties(array(
            'Unfound Property 1' => '//notfoundXpath1',
            'Unfound Property 2' => '//notfoundXpath2'
        
        ));
        $this->xmlReleaseFile->load();
        
        $expectedResult = json_encode(array(
            'Unfound Property 1' => 'not found !',
            'Unfound Property 2' => 'not found !'
        ));
        
        $this->assertTrue($expectedResult == $this->xmlReleaseFile->getPropertiesJson());
        $this->assertEquals(2, count($this->xmlReleaseFile->getWarnings()));
    }
}