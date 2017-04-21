<?php

namespace Bbr\VersionViewerBundle\Applications\AuthenticationHandler;



use Bbr\VersionViewerBundle\Applications\ReleaseFileLoader\AbstractReleaseFileLoader;

/**
 * Interface to provide authentication
 *
 * @author bbonnesoeur
 */
interface AuthenticationHandler {

    public function authenticate(AbstractReleaseFileLoader $loader);

}