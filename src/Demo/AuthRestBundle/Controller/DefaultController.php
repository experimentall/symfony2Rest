<?php

namespace Demo\AuthRestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Demo\AuthRestBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * Secure area.
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()->getRepository('DemoAuthRestBundle:User')
            ->findAll();

        return $this->render(
            'DemoAuthRestBundle:Default:index.html.twig',
            array('users' => $users)
        );
    }


    public function loginAction(Request $request)
    {
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'DemoAuthRestBundle:Default:login.html.twig',
            array(
                'last_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }
}
