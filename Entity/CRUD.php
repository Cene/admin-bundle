<?php

namespace Mesalab\Bundle\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;

/**
 * @MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 */
class CRUD
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="position", type="integer", nullable=true)
	*/
 	private $position;

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



	public function __construct()
	{

	}

	/**
	 * @ORM\PrePersist
	 */
	public function setCreatedValue()
	{
	    $this->setCreated(new \DateTime());
		$this->setPosition(0);
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
     * @return Category
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
     * @return Category
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
     * @return Category
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
}
