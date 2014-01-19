<?php
/**
 * Created by m.swietochowski
 */

namespace Application\Model;


class Marker
{
    /**
     * @var mixed
     */
    protected $code;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $latitude;
    /**
     * @var string
     */
    protected $longitude;
    /**
     * @var int
     */
    protected $count;

    /**
     * @param string $code
     * @param string $name
     * @param string $latitude
     * @param string $longitude
     * @param int $count
     */
    public function __construct($code, $name, $latitude = null, $longitude = null, $count = null)
    {
        $this->setCode($code);
        $this->setName($name);
        if ($latitude) {
            $this->setLatitude($latitude);
        }
        if ($longitude) {
            $this->setLongitude($longitude);
        }
        if ($count) {
            $this->setCount($count);
        }
    }

    public function toArray()
    {
        return array(
            $this->getCode() => array(
                'code'  => $this->getCode(),
                'name'  => $this->getName(),
                'count' => (int) $this->getCount(),
                'point' => array(
                    'latitude'  => $this->getLatitude(),
                    'longitude' => $this->getLongitude(),
                ),
            ),
        );
    }

    /**
     * @param mixed $code
     * @return Marker
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $count
     * @return Marker
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param string $latitude
     * @return Marker
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $longitude
     * @return Marker
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $name
     * @return Marker
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}