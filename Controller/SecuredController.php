<?php

namespace Mesalab\Bundle\AdminBundle\Controller;

use Mesalab\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecuredController extends Controller
{
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    public function logoutAction()
    {
        // The security layer will intercept this request
    }

	public function loginAction(Request $request)
	{
		$session = $request->getSession();
		// get the login error if there is one
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(
				SecurityContext::AUTHENTICATION_ERROR
			);
		} else {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			$session->remove(SecurityContext::AUTHENTICATION_ERROR);
		}

		return $this->render(
			'MesalabAdminBundle:Secured:login.html.twig',
			array(
				// last username entered by the user
				'last_username' => $session->get(SecurityContext::LAST_USERNAME),
				'error'         => $error,
			)
		);
	}

}
