<?php

namespace Mesalab\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mesalab\Bundle\AdminBundle\Entity\User;
use Mesalab\Bundle\AdminBundle\Form\Type\UserType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Site\Bundle\AdminBundle\Controller\CRUDController;

class AdminUserController extends CRUDController
{

	public function newItem()
	{
		return new AdminUser();
	}

	public function newForm()
	{
		return new AdminUserType();
	}

}
