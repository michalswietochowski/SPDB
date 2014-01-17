<?php
/**
 * Created by m.swietochowski
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class County
 * @package Application\Entity
 *
 * @ORM\Entity(repositoryClass="Application\Repository\County")
 * @ORM\Table(name="US_COUNTIES")
 */
class County
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=5)
     * @var string
     */
    protected $countySkid;
    /**
     * @ORM\Column(type="string", length=32)
     * @var string
     */
    protected $name;
    /**
     * @ORM\Column(type="string", length=25)
     * @var string
     */
    protected $stateName;
    /**
     * @ORM\Column(type="string", length=2)
     * @var string
     */
    protected $stateFips;
    /**
     * @ORM\Column(type="string", length=3)
     * @var string
     */
    protected $cntyFips;
}