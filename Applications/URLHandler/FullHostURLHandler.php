<?php

namespace Bbr\VersionViewerBundle\Applications\URLHandler;

use Bbr\VersionViewerBundle\Applications\URLHandler\URLHandler;

/**
 * URL Handler for a full host.
 * (ie : not templated or anything possibility)
 * 
 * @author bbonnesoeur
 *        
 */
class FullHostURLHandler extends AbstractURLHandler {

	public function setHost($host) {
		$this->host = $host;
	}
	
	/**
	 * env useless for this handler but usefull for TemplatedHostURlHandler and defined by the interface (not so good)
	 *
	 * @see \Bbr\VersionViewerBundle\Applications\URLHandler\URLHandler::getUrl()
	 */
	public function getHost($env = null) {
	    return $this->host;
	}
	
}
