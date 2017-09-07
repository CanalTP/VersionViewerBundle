<?php
namespace Bbr\VersionViewerBundle\Controller;

use Bbr\VersionViewerBundle\Applications\Exporter\CsvExporter;
use Bbr\VersionViewerBundle\Applications\Exporter\ExporterResponse;

class ExportController extends BaseController
{

    /**
     * return a csv formated file with the application instance version number
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportApplicationVersionAction($appKey)
    {
        $context = $this->getAppContext();
        
        $application = $context->getApplication($appKey);
        if (! $application) {
            throw $this->createNotFoundException('Application #' . $appKey . ' does not exist.');
        }
        
        $application->loadVersion();
        
        $exporter = new CsvExporter();
        $exporter->setDatas($application);
        
        $response = new ExporterResponse($exporter);
                
        return $response->updateContent();
    }
}