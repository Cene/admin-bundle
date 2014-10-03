<?php

namespace |namespace|\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Mesalab\AdminBundle\Entity\CRUD;

/**
 * |entity|
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class |entity| extends CRUD
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

}
