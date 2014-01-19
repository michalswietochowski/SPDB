<?php
/**
 * Created by m.swietochowski
 */

namespace Application\Repository;

use Application\Model\Marker;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class Issue
 * @package Application\Repository
 */
class Issue extends AbstractRepository
{
    const DEFAULT_ZOOM = 5;

    protected $table = 'US_ISSUES_3';

    protected $zoomLevels = array(
        'country' => 3,
        'state'   => 6,
        'county'  => 12,
        'local'   => 15
    );

    /**
     * @param array $params
     * @return array
     */
    public function getMarkers($params = array())
    {
        $zoom = isset($params['zoom']) ? $params['zoom'] : self::DEFAULT_ZOOM;

        $markers = array();
        if ($zoom <= $this->zoomLevels['country']) {
            $markers = $this->getMarkersForCountry($params);
        } else if ($zoom > $this->zoomLevels['country'] && $zoom <= $this->zoomLevels['state']) {
            $markers = $this->getMarkersForStates($params);
        } else if ($zoom > $this->zoomLevels['state'] && $zoom <= $this->zoomLevels['county']) {
            $markers = $this->getMarkersForCounties($params);
        } else if ($zoom > $this->zoomLevels['county']) {
            $markers = $this->getMarkersForLocalArea($params);
        }
        return $markers;
    }

    /**
     * @param array $params
     * @return QueryBuilder
     */
    protected function getMarkersCountQb($params = array())
    {
        $conn = $this->getConnection();
        $qb   = $conn->createQueryBuilder();
        return $qb->select('COUNT(i.ID) COUNT')
            ->from($this->table, 'i');
    }

    /**
     * @param array $params
     * @return array
     */
    public function getMarkersForCountry($params = array())
    {
        $countryLat = 38.50;
        $countryLng = -97.50;
        $id         = 0;
        $name       = 'United States of America';

        $conn = $this->getConnection();
        $qb   = $this->getMarkersCountQb($params);

        $stmt   = $conn->executeQuery($qb->getSQL());
        $marker = new Marker($id, $name, $countryLat, $countryLng, $stmt->fetchColumn());
        return $marker->toArray();
    }

    /**
     * @param array $params
     * @return QueryBuilder
     */
    protected function getMarkersForStatesQb($params = array())
    {
        return $this->getMarkersCountQb($params)
            ->select(array(
                'COUNT(i.ID) COUNT',
                'i.STATE_SKID ID',
                's.STUSPS STATE_CODE',
                's.NAME',
                's.LATITUDE',
                's.LONGITUDE',
            ))
            ->from('US_STATE', 's')
            ->where('i.STATE_SKID = s.STATE_SKID')
            ->groupBy(array(
                'i.STATE_SKID',
                's.STUSPS',
                's.NAME',
                's.LATITUDE',
                's.LONGITUDE',
            ));
    }

    /**
     * @param array $params
     * @return array
     */
    public function getMarkersForStates($params = array())
    {
        $conn = $this->getConnection();
        $qb   = $this->getMarkersForStatesQb($params);
        $stmt = $conn->executeQuery($qb->getSQL());
        $rows = $stmt->fetchAll();

        $markers = array();
        foreach ($rows as $row) {
            $marker = new Marker($row['ID'], $row['NAME'], $row['LATITUDE'], $row['LONGITUDE'], $row['COUNT']);
            $markers = array_merge($markers, $marker->toArray());
        }
        return $markers;
    }

    /**
     * @param array $params
     * @return QueryBuilder
     */
    protected function getMarkersForCountiesQb($params = array())
    {
        return $this->getMarkersCountQb($params)
            ->select(array(
                'COUNT(i.ID) COUNT',
                'i.COUNTY_SKID ID',
                'c.NAME',
                'c.LATITUDE',
                'c.LONGITUDE',
            ))
            ->from('US_COUNTIES', 'c')
            ->where('i.COUNTY_SKID = c.COUNTY_SKID')
            ->groupBy(array(
                'i.COUNTY_SKID',
                'c.NAME',
                'c.LATITUDE',
                'c.LONGITUDE',
            ));
    }

    /**
     * @param array $params
     * @return array
     */
    public function getMarkersForCounties($params = array())
    {
        $conn = $this->getConnection();
        $qb   = $this->getMarkersForCountiesQb($params);
        $stmt = $conn->executeQuery($qb->getSQL());
        $rows = $stmt->fetchAll();

        $markers = array();
        foreach ($rows as $row) {
            $marker = new Marker($row['ID'], $row['NAME'], $row['LATITUDE'], $row['LONGITUDE'], $row['COUNT']);
            $markers = array_merge($markers, $marker->toArray());
        }
        return $markers;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getMarkersForLocalArea($params = array())
    {
        return array();
    }
}