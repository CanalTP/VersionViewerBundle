<?php
namespace Bbr\VersionViewerBundle\Applications\AuthenticationHandler;

use Bbr\VersionViewerBundle\Applications\AuthenticationHandler\HttpHeader;
/**
 * @author bbonnesoeur
 * responsible of instanciation of Authentication handler.
 *
 */
class AuthenticationHandlerFactory {

  private static $_instance = null;

  /**
   * prevent instanciation
   * @return null;
   */
  private function __construct(){
    return null;
  }

  /**
   * retourne une instance singleton de la factory
   * @return \Bbr\VersionViewerBundle\Applications\AuthenticationHandler\AuthenticationHandlerFactory
   */
  public static function getInstance() {
    if(is_null(self::$_instance)) {
      self::$_instance = new AuthenticationHandlerFactory();
    }
    return self::$_instance;
  }


  /**
   * instancie un fichier d'interface vers le fichier release selon le type demandé et les paramètres passés.
   *
   * @param array $authHandlerConfig type of config
   * @return \Bbr\VersionViewerBundle\Applications\AuthenticationHandler\AuthenticationHandler
   *
   * @throws \InvalidArgumentException if unknow type of authenticationHandler
   */
  public static function getAuthenticationHandler($authHandlerConfig){

    switch ($authHandlerConfig['handler']){
      case 'HttpHeader' :
          return new HttpHeader($authHandlerConfig['params']);
        break;
      case 'URLParameter' :
          return new URLParameter($authHandlerConfig['params']);
        break;
      default :
        throw new \InvalidArgumentException("No Autentication Handler of type '".$authHandlerConfig['handler']."' available !", 500);
    }
  }

}
