<?php
namespace KimaiPlugin\HttpRemoteAuthBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use KimaiPlugin\HttpRemoteAuthBundle\Controller\HttpRemoteAuthController;

class HttpRemoteAuthEventListener
{
    
    public function __construct(HttpRemoteAuthController $rac)
    {
        $this->remote_auth_controller = $rac; 
    }


    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }
        $msg = $this->remote_auth_controller->loginRemoteAuth($event->getRequest());
        if ($msg["user"]) {
            return; 
        }

        $response = new Response();
        $response->setContent("i hate this shit");
        $event->setResponse($response);
    }
}