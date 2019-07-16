<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
/*
 * @Route(Path="/")
 */
 class SecurityController extends Controller
 {

/**
     * @param EventDispatcherInterface $dispatcher
     * @param WidgetRepository $repository
     * @param array $dashboard
     */
    public function __construct(EventDispatcherInterface $dispatcher )
    {
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * @Route(Path="/sso", name="login", methods={"GET"})
     */
    public function login(Request $r)
    {
        // get the login error if there is one
        #$error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
	    #$lastUsername = $authenticationUtils->getLastUsername();
	return $this->redirect('/oauth2/auth');
#	return $this->render('security/sso.html.twig');
    }
}
