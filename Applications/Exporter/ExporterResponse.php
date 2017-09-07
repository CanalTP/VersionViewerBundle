<?php
namespace Bbr\VersionViewerBundle\Applications\Exporter;

use Symfony\Component\HttpFoundation\Response;

class ExporterResponse extends Response
{

    /**
     *
     * @var Exporter the exporter 
     */
    private $exporter;

    public function __construct($exporter, $status = 200, $headers = array())
    {
        parent::__construct('', $status, $headers);
        $this->exporter = $exporter;
    }

    /**
     *
     * @return \Bbr\VersionViewerBundle\Applications\Exporter\ExporterResponse
     */
    public function updateContent()
    {
        $this->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $this->exporter->getFilename()));
        if (! $this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/csv');
        }
        return $this->setContent($this->exporter->export());
    }
}