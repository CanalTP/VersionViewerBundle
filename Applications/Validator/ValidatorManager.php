<?php

namespace Bbr\VersionViewerBundle\Applications\Validator;

/**
 *
 * @author bbonnesoeur
 */
class ValidatorManager {
    protected $error = array ();
    protected $warning = array ();
    protected $info = array ();

    /**
     * ajoute un message selon le niveau passé en paramètre
     *
     * @param string $level
     *            niveau du message parmis VersionValidator::ERROR, VersionValidator::WARNING, VersionValidator::INFO
     * @param string $message
     *            le message
     *            TODO cetet méthode ne devrai tpas être public, il ne devrait y avoir que les validateurs qui y ont accès.
     */
    public function addMessage($level = self::INFO, $message) {
        switch ($level) {
            case AbstractValidator::ERROR :
                array_push ( $this->error, $message );
                break;
            case AbstractValidator::WARNING :
                array_push ( $this->warning, $message );
                break;
            case AbstractValidator::INFO :
                array_push ( $this->info, $message );
        }
    }

    /**
     * retourne tous les messages (error, warning, info)
     *
     * @return array tableau contenant les tableaux de chaque niveau d'erreur
     */
    public function getMessages() {
        return array (
                'error' => $this->error,
                'warning' => $this->warning,
                'info' => $this->info
        );
    }
}