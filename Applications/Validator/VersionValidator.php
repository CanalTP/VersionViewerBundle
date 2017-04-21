<?php
namespace Bbr\VersionViewerBundle\Applications\Validator;

use Bbr\VersionViewerBundle\Applications\ReleaseFile\ReleaseFile;
use Bbr\VersionViewerBundle\Applications\Validator\AbstractValidator;

/**
 * This Class validate that two instance are in same version
 *
 * @author bbonnesoeur
 *        
 *         TODO To be fully independant rules should be put in configuration and the object instanciated by this config.
 *         Here rules are hardcoded and make coupling between env and the class which call the validator.
 */
class VersionValidator extends AbstractValidator
{

    /**
     * Check if two instance are in the same version.
     * Add a warning message if version are different.
     *
     * @param ReleaseFile $file1
     *            a release Fille
     * @param ReleaseFile $file2
     *            another release file
     */
    public function validate(ReleaseFile $file1, ReleaseFile $file2 = null)
    {
        if ($file1->isValid() && $file2->isValid()) {
            if ($file1->getComparisonValue() != $file2->getComparisonValue()) {
                $this->addMessage($this::WARNING, "Environments " . $file1->getEnvironment()
                    ->getName() . " and " . $file2->getEnvironment()
                    ->getName() . " aren't in same version !");
            }
        }
    }
}
