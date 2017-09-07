<?php
namespace Bbr\VersionViewerBundle\Applications\Exporter;

use Bbr\VersionViewerBundle\Applications\Application;

class CsvExporter extends AbstractExporter
{

    private $application;

    private $titles = array();

    private $rows = array();

    /**
     *
     * @param Application $application
     *            data to export
     */
    public function setDatas($application)
    {
        $this->application = $application;
    }

    public function export()
    {
        $this->getDatas();
        
        $handle = fopen('php://output', 'w+');
        
        fputcsv($handle, $this->titles, ';');
        fputcsv($handle, $this->rows, ';');
        
        fclose($handle);
    }

    private function getDatas()
    {
        foreach ($this->application->getAppInstance() as $appInstance) {
            array_push($this->titles, $appInstance->getEnvironment()->getName());
            array_push($this->rows, $appInstance->getVersionValue());
        }
    }
}