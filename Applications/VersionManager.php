<?php
namespace Bbr\VersionViewerBundle\Applications;

use Bbr\VersionViewerBundle\Applications\ColorManager;
use Bbr\VersionViewerBundle\Applications\AppInstance;

class  VersionManager{

  private $colorManager;

  private $oldestReleaseTag;

  function __construct(ColorManager $colorManager){
    $this->colorManager = $colorManager;
  }


  /**
   * gère les opérations connexes aux instances
   * gère les données relatives aux instances :
   * - gestion des dates de release
   * - attribution des couleurs aux releases
   *
   * @param AppInstance $appInstance : l'instance à gérer
   */
  public function manageInstance(AppInstance $appInstance){

    //attribution d'une couleur à la release si l'instance est valide
    if($appInstance->getReleaseFile()->isValid()){
      $this->colorManager->attributeColor($appInstance);
    }
  }

  /**
   * @deprecated n'a pas vraiment d'utilité
   * retourne le plus ancien tag pour l'application
   */
  public function getOldestreleaseTag(){
    return $this->oldestReleaseTag;
  }

}