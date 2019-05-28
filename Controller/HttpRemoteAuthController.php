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
/**
 * @Route(path="/remote_auth")
 */
class HttpRemoteAuthController extends AbstractController
{
   
    public function __construct()
    {
    }
    /**
     * @Route(path="", name="remote_auth", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
       
        return $this->render('Hello World');
    }
    
}