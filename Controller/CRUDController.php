<?php

namespace Mesalab\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Mesalab\Bundle\AdminBundle\Controller\iCRUDController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CRUDController extends Controller
{
	private $pager;
	private $sort;
	private $options;

	private $parameters;
	private $namespace;
	private $className;
	private $routingPrefix;

	/*
	 * Set default bundle parameters
	 */
	private function setBundleParams()
	{
		$this->getParameters();
		$this->routingPrefix = $this->parameters['routing_prefix'];
		$this->pager = array(
			'page' => 1,
			'perpage' => $this->parameters['pager']['perpage']
		);
		$this->sort = array(
			'field' => $this->parameters['sort']['field'],
			'order'=> $this->parameters['sort']['order']
		);
		$this->options = array();
	}

	/*
	 * Get Bundle configuration parameters from parameters.yml
	 * Overwrite to specify a different parameters.yml structure
	 */
	public function getParameters()
	{
		$matches = array();
		$controller = $this->getRequest()->attributes->get('_controller');
		preg_match('/(.*)\\\Bundle\\\(.*)\\\Controller\\\(.*)Controller::(.*)Action/', $controller, $matches);
		if(empty($matches)){
			preg_match('/(.*)\\\(.*)\\\Controller\\\(.*)Controller::(.*)Action/', $controller, $matches);
		}
		$this->namespace = $matches[1].$matches[2];
		$this->className = $matches[3];
		$this->parameters = $this->container->getParameter($this->namespace)[$this->className];
	}

	private function bundleViewParams()
	{
		return array(
			'routingPrefix' => $this->routingPrefix
		);
	}

	/*
	 * default functions.
	 * Must be overwritten
	 */
	public function newItem(){ return false; }
	public function newForm() { return false; }


	public function indexAction(Request $request)
	{
		$this->setBundleParams();

		// get pager GET params
		$pager = $request->query->get('pager', $this->pager);
		$options = $request->query->get('options', $this->options);
		$sort = $request->query->get('sort', $this->sort);

		// send params to view
		return $this->render($this->namespace . ':' . $this->className . ':index.html.twig', array(
			'bundleParams' => $this->bundleViewParams(),
			'pager' => $pager,
			'sort' => $sort,
			'options' => $options
		));
	}


	public function newAction(Request $request)
	{
		$this->setBundleParams();

        $item = $this->newItem();
		$form = $this->createForm($this->newForm(), $item, array(
			'action' => $this->generateUrl($this->routingPrefix . '_new').'?'.$request->getQueryString(),
		));

		$form->handleRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($item);
			$em->flush();
			return $this->redirect($this->generateUrl($this->routingPrefix).'?'.$request->getQueryString());
		}

        return $this->render($this->namespace . ':' . $this->className . ':new.html.twig', array(
			'bundleParams' => $this->bundleViewParams(),
            'form' => $form->createView(),
			'queryString' => $request->getQueryString()
        ));
	}


	public function editAction($id, Request $request)
	{
		$this->setBundleParams();

		$repository = $this->getDoctrine()->getRepository($this->namespace . ':' . $this->className);
        $item = $repository->find($id);
		if (!$item) {
			throw $this->createNotFoundException(
				'No item found for id '.$id
			);
		}

		$form = $this->createForm($this->newForm(), $item, array(
			'action' => $this->generateUrl($this->routingPrefix . '_edit', array('id'=>$id)).'?'.$request->getQueryString(),
		));

		$form->handleRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($item);
			$em->flush();
			return $this->redirect($this->generateUrl($this->routingPrefix).'?'.$request->getQueryString());
		}

        return $this->render($this->namespace . ':' . $this->className . ':new.html.twig', array(
			'bundleParams' => $this->bundleViewParams(),
            'form' => $form->createView(),
			'queryString' => $request->getQueryString()
        ));
	}


	public function pagedAction(Request $request)
	{
		$this->setBundleParams();

		$pager = $request->query->get('pager', $this->pager);
		$options = $request->query->get('options', $this->options);
		$sort = $request->query->get('sort', $this->sort);

		$repository = $this->getDoctrine()->getRepository($this->namespace . ':' . $this->className);

		$sql = $repository->createQueryBuilder('a');
		$sql->addOrderBy('a.'.$sort['field'], $sort['order']);
		$sql->addOrderBy('a.id', 'DESC');

		if($options !== null){
			foreach($options as $option => $value){
				$sql->where('a.'.$option.' LIKE :'.$option)
					->setParameter($option, '%'.$value.'%');
			}
		}

		// total items
		$query = $sql->getQuery();
		$totalItems = count($query->getResult());
		$pages = ceil($totalItems / $pager['perpage']);

		// add limit
		$firstResult = ($pager['page'] * $pager['perpage']) - $pager['perpage'];
		if($firstResult >= $totalItems){
			$pager['page'] = 1;
			$sql->setFirstResult(0);
		}
		else{
			$sql->setFirstResult($firstResult);
		}
		$sql->setMaxResults($pager['perpage']);

		// items with limit
		$query = $sql->getQuery();
		$items = $query->getResult();

		return $this->render($this->namespace . ':' . $this->className . ':paged.html.twig',array(
			'page' => $pager['page'],
			'pages' => $pages,
			'sort' => $sort,
			'items' => $items,
			'queryString' => $request->getQueryString()
		));
	}


	public function sortAction(Request $request)
	{
		$this->setBundleParams();

		$repository = $this->getDoctrine()->getRepository($this->namespace . ':' . $this->className);
		$em = $this->getDoctrine()->getManager();

		if ($request->isMethod('POST'))
		{
			$itemsList = $request->request->get('list');
			foreach($itemsList as $k=>$v)
			{
		        $item = $repository->find($v);
				$item->setPosition((int)$k);

				$em->persist($item);
				$em->flush();
			}

			return new JsonResponse(array('success' => 1));
		}
		else{
			$sql = $repository->createQueryBuilder('a');
			$sql->orderBy('a.position', 'ASC');
			$query = $sql->getQuery();
			$items = $query->getResult();

			return $this->render($this->namespace . ':' . $this->className . ':sort.html.twig',array(
				'bundleParams' => $this->bundleViewParams(),
				'items' => $items
			));
		}

	}

}
