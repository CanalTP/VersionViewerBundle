<?php
use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\JsonPropertieTransformer;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\Configuration\ReleaseFileConfiguration;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\JsonReleaseFile;
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\LocalReleaseFileLoader;

/**
 *
 * @author bbonnesoeur
 *        
 *         Implement test on method defined in the AbstractReleaseFile Class.
 */
class JsonReleaseFileTest extends \PHPUnit_Framework_TestCase
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
    private $jsonReleaseFile;

    public function setUp()
    {
        parent::setUp();
        
        $this->env = new Environment(array(
            'name' => 'customer',
            'trigram' => 'cus'
        ));
        
        $this->releaseFileConfiguration = new ReleaseFileConfiguration();
        $this->releaseFileConfiguration->setPropertieTransformer(new JsonPropertieTransformer());
        
        $this->jsonReleaseFile = new JsonReleaseFile($this->env, $this->releaseFileConfiguration);
        $this->jsonReleaseFile->setLoader(new LocalReleaseFileLoader(__DIR__ . '/../Fixtures/jsonReleaseFile.json'));
    }

    /**
     * Test property at root level and at object level
     */
    public function testFilteredDataFoundPropertie()
    {
        $this->releaseFileConfiguration->setFilteredProperties(array(
            'status' => 'application.status',
            'traitement' => 'connector.traitement',
            'testing' => 'testing'
        ));
        
        $this->jsonReleaseFile->load();
        
        $expectedResult = json_encode(array(
            'status' => 'OK',
            'traitement' => true,
            'testing' => 'test'
        ));
        
        $this->assertTrue($expectedResult == $this->jsonReleaseFile->getPropertiesJson());
    }

    /**
     * Test that unfound properties are wall handled
     */
    public function testFilteredDataNotFoundPropertie()
    {
        $this->releaseFileConfiguration->setFilteredProperties(array(
            'version' => 'connector.version',
            'traitement' => 'connector.traitement',
            'mails' => 'sms'
        ));
        
        $this->jsonReleaseFile->load();
        
        $expectedResult = json_encode(array(
            'version' => 'not found !',
            'traitement' => true,
            'mails' => 'not found !'
        ));
        
        $this->assertTrue($expectedResult == $this->jsonReleaseFile->getPropertiesJson());
    }

    /**
     * Test if filtered data had only two warning if the first object was not found
     * ie : if we are looking for status.status => should have only one warning for property status
     */
    public function testFilteredDataObjectHasWarning()
    {
        $this->releaseFileConfiguration->setFilteredProperties(array(
            'status' => 'status.status',
            'connexion' => 'connexion'
        ));
        
        $this->jsonReleaseFile->load();
        
        $expectedResult = json_encode(array(
            'status' => 'not found !',
            'connexion' => 'not found !'
        ));
        $this->assertTrue($expectedResult == $this->jsonReleaseFile->getPropertiesJson());
        $this->assertEquals(2, count($this->jsonReleaseFile->getWarnings()));
    }
}