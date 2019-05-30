<?php
/*
 * This file is part of the Kimai CustomCSSBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace KimaiPlugin\HttpRemoteAuthBundle\Controller;
use App\Controller\AbstractController;
use KimaiPlugin\CustomCSSBundle\Entity\CustomCss;
use KimaiPlugin\CustomCSSBundle\Form\CustomCssType;
use KimaiPlugin\CustomCSSBundle\Repository\CustomCssRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use FOS\UserBundle\Model\UserManagerInterface;
use App\Entity\User;
use KimaiPlugin\HttpRemoteAuthBundle\Security\HttpRemoteAuthAuthenticator;

/**
 *  @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
 */
class HttpRemoteAuthController extends AbstractController
{
    public const HEADER_USERNAME = 'X-AUTH-USER';
    public const HEADER_EMAIL = 'X-AUTH-EMAIL';	

    /**
     * Undocumented function
     *
     * @param UserManagerInterface $userManager
     * @param GuardAuthenticatorHandler $authHandler
     * @param Authenticator $authenticator
     */
    public function __construct(GuardAuthenticatorHandler $authHandler, UserManagerInterface $userManager, HttpRemoteAuthAuthenticator $authenticator)
    {
        $this->userManager = $userManager;
        $this->authHandler = $authHandler;
        $this->authenticator = $authenticator;
    }

    private function loginRemoteAuth($request) {
        $username = $request->headers->get(self::HEADER_USERNAME);
        $email = $request->headers->get(self::HEADER_EMAIL);

        $msg = [];

        if ( ! $username || ! $email ) {
            $msg[]=["action" => "Error", "data" => "Username missing"];
            return ["msg"=>$msg, "user"=>null];
        }

        $_u = $this->userManager->findUserByUsername($username);
        if ( ! $_u ) {
            $msg[]=[ "action"=> "Creating", "data" => $username];

            $user = $this->userManager->createUser();
            $user->setEnabled(true);
            $user->setUsername("$username");
            $user->setPassword("pp-$username");
            $user->setEmail("xx-$email");
            $user->setRoles([User::ROLE_USER]);
            $this->userManager->updateUser($user);

            $_u = $user;
        }
        $msg[]=[ "action" => "Authenticating", "data" => $username];

        $this->authHandler->authenticateUserAndHandleSuccess($_u, $request, $this->authenticator, 'secured_area');
        return ["msg"=>$msg,"user"=>$_u];
    }
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     * @Route(path="/remote/auth", name="remote_auth", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
        $msg = $this->loginRemoteAuth($request);
        if ($msg["user"]) {
            return $this->redirectToRoute('homepage');
        }
        return $this->render('@HttpRemoteAuth/index.html.twig',[ "auth_msg" => $msg['msg']]);
    }
    
}