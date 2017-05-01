<?php
namespace Bbr\VersionViewerBundle\Controller;

use Bbr\VersionViewerBundle\Applications\AppContext;
use Bbr\VersionViewerBundle\Entity\Contact;
use Bbr\VersionViewerBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    /**
     *
     * @var array app Configuration
     */
    private $appContext;

    /**
     * handle home page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $context = $this->getAppContext();
        return $this->render('BbrVersionViewerBundle:Default:index.html.twig', array(
            'context' => $context
        ));
    }

    /**
     * handle help page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function helpAction()
    {
        $kernel = $this->get('kernel');
        $filePath = $kernel->locateResource('@BbrVersionViewerBundle/Resources/doc/index.rst');
        $document = new \ezcDocumentRst();
        $document->options->xhtmlVisitor = 'ezcDocumentRstXhtmlBodyVisitor';
        $document->loadFile($filePath);
        $docbook = $document->getAsXhtml();
        
        return $this->render('BbrVersionViewerBundle:Help:help.html.twig', array(
            'content' => $docbook->save()
        ));
    }

    /**
     * handle feedback form action
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function feedBackAction(Request $request)
    {
        $context = $this->getAppContext();
        $contact = new Contact();
        
        $form = $this->createForm(new ContactType(), $contact);
        
        if ($request->getMethod() == 'POST') {
            
            $form->handleRequest($request);
            if ($form->isValid()) {
                
                $message = \Swift_Message::newInstance()->setSubject('Feedback VersionViewer')
                    ->setFrom($context->getFeedBackEmailSender())
                    ->setTo($context->getFeedBackEmailReceiver())
                    ->setBody($this->renderView('BbrVersionViewerBundle:FeedBack:FeedBackEmail.txt.twig', array(
                    'contact' => $contact
                )));
                $this->get('mailer')->send($message);
                
                return new Response('Message Send ! Thank you for your feedback !', '200');
            }
        }
        
        return $this->render('BbrVersionViewerBundle:FeedBack:feedBack.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * load all the application instance (all environnment) and return the result in json 
     *
     * @param
     *            string application key
     *            
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadApplicationAction($appKey)
    {
        $context = $this->getAppContext();
        
        $application = $context->getApplication($appKey);
        if (! $application) {
            throw $this->createNotFoundException('Application #' . $appKey . ' does not exist.');
        }
        
        $application->loadVersion();
        
        $application->validateVersion();
        
        $response = new Response($this->renderView('BbrVersionViewerBundle:Default:application.json.twig', 
            // @TODO appinstances devrait faire appel à une méthode de l'applciation qui retourne un tableau préformatté pour json_encode
            array(
                'appInstances' => $application->getAppInstance(),
                'messages' => json_encode($application->getVersionValidator()
                    ->getMessages())
            )));
        return $response;
    }

    /**
     *
     * load the application instance for the given environment and return the result in json
     *
     * @param
     *            string
     *            application key 
     * @param
     *            environnement
     *            
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadAppInstanceAction($appKey, $env)
    {
        $context = $this->getAppContext();
        
        $application = $context->getApplication($appKey);
        if (! $application) {
            throw $this->createNotFoundException('Application #' . $appKey . ' does not exist.');
        }
        
        if (! $application->loadVersion($env)) {
            throw $this->createNotFoundException('Application #' . $appKey . ' Can\'t be loaded in #' . $env . ' environment : ' . $application->getAppInstance($env)
                ->getErrorsAsString());
        }
        
        return $this->render('BbrVersionViewerBundle:Default:instance.json.twig', array(
            'properties' => $application->getAppInstance($env)
                ->getReleaseFile()
                ->getPropertiesJson(),
            'errors' => $application->getAppInstance($env)
                ->getReleaseFile()
                ->getErrors(),
            'warnings' => $application->getAppInstance($env)
                ->getReleaseFile()
                ->getWarnings()
        ));
    }

    /**
     * Initialize context application
     * - build all application according to configuration
     *
     * @return \Bbr\VersionViewerBundle\Applications\AppContext
     */
    private function getAppContext()
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
