<?php

namespace Mesalab\Bundle\AdminBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Mesalab\Bundle\AdminBundle\Entity\CRUD;

/**
 * User
 *
 * @ORM\Table(name="admin_user")
 * @ORM\Entity(repositoryClass="Mesalab\Bundle\AdminBundle\Entity\AdminUserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class AdminUser implements UserInterface, \Serializable
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	public $id;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="position", type="integer", nullable=true)
	 */
	private $position;

	/**
	 * @ORM\Column(type="string", length=25, unique=true)
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", length=64)
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=60, unique=true, nullable=true)
	 */
	private $email;

	/**
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	private $isActive;

	/**
	 * @var datetime
	 *
	 * @ORM\Column(name="created", type="datetime", nullable=true)
	 */
	private $created;

	/**
	 * @var datetime
	 *
	 * @ORM\Column(name="updated", type="datetime", nullable=true)
	 */
	private $updated;








	/**
	 * @ORM\PrePersist
	 */
	public function setCreatedValue()
	{
	    $this->setCreated(new \DateTime());
		$this->setPosition(0);
		$this->setIsActive(true);
	}

	/**
	 * @ORM\PreUpdate
	 */
	public function setUpdatedValue()
	{
	    $this->setUpdated(new \DateTime());
	}




	/**
	 * Get id
	 *
	 * @return integer
	 */
	 public function getId()
	 {
		 return $this->id;
	 }

	/**
	 * Set position
	 *
	 * @param integer $position
	 * @return AdminUser
	 */
	public function setPosition($position)
	{
		$this->position = $position;

		return $this;
	}

	/**
	 * Get position
	 *
	 * @return integer
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * Set created
	 *
	 * @param \DateTime $created
	 * @return AdminUser
	 */
	public function setCreated($created)
	{
		$this->created = $created;

		return $this;
	}

	/**
	 * Get created
	 *
	 * @return \DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * Set updated
	 *
	 * @param \DateTime $updated
	 * @return AdminUser
	 */
	public function setUpdated($updated)
	{
		$this->updated = $updated;

		return $this;
	}

	/**
	 * Get updated
	 *
	 * @return \DateTime
	 */
	public function getUpdated()
	{
		return $this->updated;
	}

	/**
	 * @inheritDoc
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @inheritDoc
	 */
	public function getSalt()
	{
		return null; // bcrypt requires no salt
	}

	/**
	 * @inheritDoc
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @inheritDoc
	 */
	public function getRoles()
	{
		return array('ROLE_USER');
	}

	/**
	 * @inheritDoc
	 */
	public function eraseCredentials()
	{
	}

	/**
	 * @see \Serializable::serialize()
	 */
	public function serialize()
	{
		return serialize(array(
			$this->id,
			$this->username,
			$this->password,
		));
	}

	/**
	 * @see \Serializable::unserialize()
	 */
	public function unserialize($serialized)
	{
		list (
			$this->id,
			$this->username,
			$this->password,
			) = unserialize($serialized);
	}

	/**
	 * Set username
	 *
	 * @param string $username
	 * @return AdminUser
	 */
	public function setUsername($username)
	{
		$this->username = $username;

		return $this;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 * @return AdminUser
	 */
	public function setPassword($password)
	{
		if (!is_null($password)) {
			$this->password = $password;
		}

		return $this;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 * @return AdminUser
	 */
	public function setEmail($email)
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Set salt
	 *
	 * @param string $salt
	 * @return AdminUser
	 */
	public function setSalt($salt)
	{
		$this->salt = $salt;

		return $this;
	}

	/**
	 * Set isActive
	 *
	 * @param boolean $isActive
	 * @return AdminUser
	 */
	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;

		return $this;
	}

	/**
	 * Get isActive
	 *
	 * @return boolean
	 */
	public function getIsActive()
	{
		return $this->isActive;
	}

}
