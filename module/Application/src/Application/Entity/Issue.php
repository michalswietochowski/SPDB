<?php
/**
 * Created by m.swietochowski
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Issue
 * @package Application\Entity
 *
 * @ORM\Entity(repositoryClass="Application\Repository\Issue")
 * @ORM\Table(name="US_ISSUES_3")
 */
class Issue
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=2)
     * @var string
     */
    protected $stateSkid;
    /**
     * @ORM\Column(type="string", length=5)
     * @var string
     */
    protected $countySkid;
    /**
     * @ORM\Column(type="decimal", precision=10, scale=6)
     * @var string
     */
    protected $latitude;
    /**
     * @ORM\Column(type="decimal", precision=10, scale=6)
     * @var string
     */
    protected $longitude;
    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $summary;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $description = null;
    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    protected $source;
    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $createdTime;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    protected $tagType = null;
}