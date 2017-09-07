<?php
namespace Bbr\VersionViewerBundle\Applications\Exporter;

abstract class  AbstractExporter implements Exporter{
    
    protected $filename = 'export.csv';
    /**
     * @return string file name
     */
    public function getFilename()
    {
        return $this->filename;
    }

    
    
    
}