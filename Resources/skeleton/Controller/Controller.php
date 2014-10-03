<?php

namespace |namespace|\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use |namespace|\Entity\|entity|;
use |namespace|\Form\Type\|entity|Type;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mesalab\Bundle\AdminBundle\Controller\CRUDController;

class |entity|Controller extends CRUDController
{
	public function newItem()
	{
		return new |entity|();
	}

	public function newForm()
	{
		return new |entity|Type();
	}
}
