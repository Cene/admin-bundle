<?php
namespace Mesalab\Bundle\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Mesalab\Bundle\AdminBundle\Entity\AdminUser;

class LoadAdminUserData implements FixtureInterface, ContainerAwareInterface
{

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * {@inheritDoc}
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		var_dump('getting container here');
		$this->container = $container;
	}

	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$user = new AdminUser();
		$user->setUsername("admin");
		$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
		$user->setPassword($encoder->encodePassword('admin', $user->getSalt()));
		$user->setEmail("admin@mail.fake");

		$manager->persist($user);

		$manager->flush();
	}
}