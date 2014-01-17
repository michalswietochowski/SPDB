<?php
/**
 * Created by m.swietochowski
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class State
 * @package Application\Entity
 *
 * @ORM\Entity(repositoryClass="Application\Repository\State")
 * @ORM\Table(name="US_STATE")
 */
class State
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=2)
     * @var string
     */
    protected $stateSkid;
    /**
     * @ORM\Column(type="string", length=2)
     * @var string
     */
    protected $region;
    /**
     * @ORM\Column(type="string", length=2)
     * @var string
     */
    protected $division;
    /**
     * @ORM\Column(type="string", length=2)
     * @var string
     */
    protected $statefp;
    /**
     * @ORM\Column(type="string", length=8)
     * @var string
     */
    protected $statens;
    /**
     * @ORM\Column(type="string", length=2)
     * @var string
     */
    protected $stusps;
    /**
     * @ORM\Column(type="string", length=100)
     * @var string
     */
    protected $name;
    /**
     * @ORM\Column(type="string", length=2)
     * @var string
     */
    protected $lsad;
    /**
     * @ORM\Column(type="string", length=5)
     * @var string
     */
    protected $mtfcc;
    /**
     * @ORM\Column(type="string", length=1)
     * @var string
     */
    protected $funcstat;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $aland;
    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $awater;
    /**
     * @ORM\Column(type="string", length=2)
     * @var string
     */
    protected $intptlat;
    /**
     * @ORM\Column(type="string", length=2)
     * @var string
     */
    protected $intptlon;
    /**
     * @ORM\Column(type="decimal", precision=12, scale=8)
     * @var string
     */
    protected $latitude;
    /**
     * @ORM\Column(type="decimal", precision=12, scale=8)
     * @var string
     */
    protected $longitude;
}