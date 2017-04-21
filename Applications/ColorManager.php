<?php
namespace Bbr\VersionViewerBundle\Applications;

use Bbr\VersionViewerBundle\Applications\AppInstance;

/**
 * Handle color attribution to instance.
 */
class ColorManager
{

    /**
     *
     * @var array[boolean] store attributed values 
     *      keys are possible value of color (bootstrap css class)
     *      values tell if color is already attributedd to a version
     *     
     */
    public $colors = array(
        'label-success' => false,
        'label-warning' => false,
        'label-info' => false,
        'label-inverse' => false
    );

    /**
     *
     * @var array[string] store color for each release
     *     
     *      keys are release number (value of the compared properties)
     *      values are the affected color.
     */
    public $releaseColors = array();

    /**
     * Assign a color to an instance according the value of it's comparison propertie
     *
     * @param AppInstance $release            
     */
    function attributeColor(AppInstance $appInstance)
    {
        $compValue = $appInstance->getReleaseFile()->getComparisonValue();
        
        if ($compValue) {
            // récupération de la couleur s'il y en à déjà une pour ce tag
            if (array_key_exists($compValue, $this->releaseColors)) {
                $color = $this->releaseColors[$compValue];
                $appInstance->setColor($color);
            }  // sinon on doit en attibuer une
else {
                foreach ($this->colors as $color => $used) {
                    if ($used == false) {
                        // attribution de la couleur à la release
                        $appInstance->setColor($color);
                        // conservation de l'attribution
                        $this->releaseColors[$compValue] = $color;
                        // couleur indisponible
                        $this->colors[$color] = true;
                        return;
                    }
                }
            }
        }
    }
}
