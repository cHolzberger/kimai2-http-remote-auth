<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\HttpRemoteAuthBundle\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use FOS\UserBundle\Model\UserManagerInterface;

use Psr\Log\LoggerInterface;

class HttpRemoteAuthAuthenticator extends AbstractGuardAuthenticator
{
    public const HEADER_USERNAME = 'X-AUTH-USER';
    public const HEADER_TOKEN = 'X-AUTH-TOKEN';	
    public const HEADER_EMAIL = 'X-AUTH-EMAIL';	

    /**
     * @var GuardAuthenticationHandler
     */
    protected $gh;
	

    protected $logger;
    protected $userManager;
    /**
     * @param GuardAuthenticationHandler $guardHandler
     * @param UserManagerInterface $um
     * @param LoggerInterface $l
     */
    public function __construct (GuardAuthenticatorHandler $gh, UserManagerInterface $um, LoggerInterface $l)
    {
	    $this->gh = $gh;
	    $this->logger = $l;
	    $this->userManager = $um;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
	    return true; 
	    $request->headers->has(self::HEADER_USERNAME);
    }

    /**
     * @param Request $request
     * @return array|bool
     */
    public function getCredentials(Request $request)
    {
	    $this->request = $request;
        $u = [
            'user' => $request->headers->get(self::HEADER_USERNAME),
            'token' => $request->headers->get(self::HEADER_TOKEN),
            'email' => $request->headers->get(self::HEADER_EMAIL),
    ];

            $this->checkUserExists($u['token'], $u['user'], $u['email']);
	    return $u;
    }

    /**
     * @param array $credentials
     * @param UserProviderInterface $userProvider
     * @return null|UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $credentials['token'] ?? null;
        $user = $credentials['user'] ?? null;
        $email = $credentials['email'] ?? null;
	

        if (empty($token) || empty($user)) {
            return null;
	}
        $_u = $userProvider->loadUserByUsername($user);

	return $_u;	
    }

    function checkUserExists($token, $username, $email) {

	$this->logger->info("Checking for User:", ["user"=>$username]);
	$_u = $this->userManager->findUserByUsername($username);
	if ( $_u ) {
		$this->logger->info("Found User in $db", ["user"=>$_u]);
	} else {
	   $this->logger->info("Creating User",["user"=>$username]);
	    $user = $this->userManager->createUser();
	    $user->setEnabled(true);
	    $user->setUsername("$username");
	    $user->setPassword("pp-$username");
	    $user->setEmail("xx-$email");
	    $user->setRoles([User::ROLE_USER]);
	    $this->userManager->updateUser($user);

	    $_u = $user;
	}
	$this->gh->authenticateUserAndHandleSuccess($_u, $this->request, $this, 'secured_area');
	return $_u;
    }

    /**
     * @param array $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->request->headers->has(self::HEADER_USERNAME);
    }
    
    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
	 
      	 // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
	    $this->request = $request;
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

}
