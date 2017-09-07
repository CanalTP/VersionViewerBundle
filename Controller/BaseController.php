<?php
namespace Bbr\VersionViewerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bbr\VersionViewerBundle\Applications\AppContext;


class BaseController extends Controller
{

    /**
     *
     * @var array app Configuration
     */
    protected $appContext;

    /**
     * Initialize context application
     * - build all application according to configuration
     *
     * @return \Bbr\VersionViewerBundle\Applications\AppContext
     */
    protected function getAppContext()
    {
        if (null === $this->appContext) {
            $appConfig = $this->container->getParameter('bbr_version_viewer.appConfig');
            $environments = $this->container->getParameter('bbr_version_viewer.environments');
            $urlHandler = $this->container->getParameter('bbr_version_viewer.urlHandler');
            $applications = $this->container->getParameter('bbr_version_viewer.applications');
            $appTypes = $this->container->getParameter('bbr_version_viewer.applications_type');
            
            $this->appContext = new AppContext($environments, $applications, $appConfig, $urlHandler, $appTypes);
        }
        
        return $this->appContext;
    }
}