<?php

namespace Bbr\VersionViewerBundle\Applications;

/**
 * class for an environment
 *
 * @author bbonnesoeur
 */
class Environment {
    /** @var string $name environment name */
    private $name;
    /** @var strin $trigram trigram's environment ie: itg => integration.*/
    private $trigram;

    function __construct($environment) {
        $this->name = $environment ['name'];
        $this->trigram = $environment ['trigram'];
    }
    /**
     * @return string environment trigram
     */
    public function getTrigram() {
        return $this->trigram;
    }
    /**
     * @return string environment name
     */
    public function getName() {
        return $this->name;
    }
}