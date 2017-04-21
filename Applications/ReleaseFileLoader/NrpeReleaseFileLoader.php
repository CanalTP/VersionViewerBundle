<?php
namespace Bbr\VersionViewerBundle\Applications\ReleaseFileLoader;

use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\ReleaseFileLoaderInterface;
use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\AbstractReleaseFileLoader;
use Bbr\VersionViewerBundle\Applications\Environment;
use Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile;

/**
 * Load a release file through the NRPE client
 * Launch a command like that : /usr/lib/nagios/plugins/check_nrpe -H my.server.host -c my_command_json
 *
 * @author bbonnesoeur
 *        
 */
class NrpeReleaseFileLoader extends AbstractReleaseFileLoader
{

    private $nrpeClientPath = '/usr/lib/nagios/plugins/check_nrpe';

    private $host;

    private $command = 'get_supervision_json';

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        
        return $this;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\ReleaseFileLoaderInterface::load()
     */
    public function load()
    {
        $content = exec($this->buildCommand(), $output, $return);
        // $content = exec('/usr/lib/nagios/plugins/check_nrpe -H my.host.fr -c get_supervision_json', $output, $return);

        // exec is successful only if the $return_var was set to 0.
        // !== means equal and identical, that is it is an integer and it also is zero.
        if ($return !== 0) {
            // dump($return);
            // dump($output);
            // dump($content);die;
            throw new ReleaseFileLoadingException($output);
        } else {
            return $content;
        }
        
        // dump($content);
        // dump($output);
        // dump($return);die;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\AbstractReleaseFileLoader::getRessourceCommand()
     */
    public function getRessourceCommand()
    {
        return $this->buildCommand();
    }

    /**
     * build the full command line to execute to retrieve the file content
     *
     * @return string the full command line with substitued parameters.
     */
    private function buildCommand()
    {
        return $this->nrpeClientPath . " -H " . $this->releaseFile->getHost() . " -c get_json -a ". $this->releaseFile->getFilePath();
        // /usr/lib/nagios/plugins/check_nrpe -H my.host.name -c get_json -a /path/to/file.ext
    }

    /**
     * handle configuration for releaseFileLoader in the ReleaseFileLoaderConfiguration section
     * At the moment handle only timeout parameter
     *
     * @param array[string] $configuration
     *            ReleaseFielLoader configuration
     *            
     * @todo REFACTO CONFIG : this config must be moved in ReleaseFileLoaderConfiguration configuration section.
     */
    public function handleConfiguration($configuration)
    {
        // @TODO need to handle configuration ?
        return $this;
    }
}