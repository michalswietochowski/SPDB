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
    /**
     * Spatial reference system ids
     */
    const SRID_WGS84 = 4326;
    const SRID_NAD83 = 4269;

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
     * @return string
     */
    public function getSql($params = array())
    {
        $zoom = isset($params['zoom']) ? $params['zoom'] : self::DEFAULT_ZOOM;

        $sql = '';
        if ($zoom <= $this->zoomLevels['country']) {
            $sql = $this->getMarkersCountQb($params)->getSQL();
        } else if ($zoom > $this->zoomLevels['country'] && $zoom <= $this->zoomLevels['state']) {
            $sql = $this->getMarkersForStatesQb($params)->getSQL();
        } else if ($zoom > $this->zoomLevels['state'] && $zoom <= $this->zoomLevels['county']) {
            $sql = $this->getMarkersForCountiesQb($params)->getSQL();
        } else if ($zoom > $this->zoomLevels['county']) {
            $sql = $this->getMarkersForLocalQb($params)->getSQL();
        }
        return $sql;
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
            $marker  = new Marker($row['ID'], $row['NAME'], $row['LATITUDE'], $row['LONGITUDE'], $row['COUNT']);
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
            $marker  = new Marker($row['ID'], $row['NAME'], $row['LATITUDE'], $row['LONGITUDE'], $row['COUNT']);
            $markers = array_merge($markers, $marker->toArray());
        }
        return $markers;
    }

    /**
     * @param array $params
     * @return QueryBuilder
     */
    protected function getMarkersForLocalQb($params = array())
    {
        $sdoRect   = $this->sdoRect(
            $params['bounds']['sw']['longitude'],
            $params['bounds']['sw']['latitude'],
            $params['bounds']['ne']['longitude'],
            $params['bounds']['ne']['latitude'],
            self::SRID_WGS84
        );
        $sdoInside = sprintf('SDO_INSIDE(i.GEO_POINT, %s) = \'TRUE\'', $sdoRect);
        return $this->getMarkersCountQb($params)
            ->select(array(
                'i.ID',
                'i.SUMMARY',
                'i.LATITUDE',
                'i.LONGITUDE',
            ))
            ->where($sdoInside);
    }

    /**
     * Get SDO_GEOMETRY for rectangle (bounds)
     * @param string $swLng south-west longitude
     * @param string $swLat south-west latitude
     * @param string $neLng north-east longitude
     * @param string $neLat north-east latitude
     * @param int|null $srid spatial reference identification system
     * @return string oracle sdo_geometry string
     */
    protected function sdoRect($swLng, $swLat, $neLng, $neLat, $srid = null)
    {
        if (!$srid) {
            $srid = 'NULL';
        }
        $sdoGeometry = "SDO_GEOMETRY(2003, %s, NULL, SDO_ELEM_INFO_ARRAY(1, 1003, 3), %s)";
        $sdoOrdinate = sprintf('SDO_ORDINATE_ARRAY(%s, %s, %s, %s)', $swLng, $swLat, $neLng, $neLat);
        return sprintf($sdoGeometry, $srid, $sdoOrdinate);
    }

    /**
     * @param array $params
     * @return array
     */
    public function getMarkersForLocalArea($params = array())
    {
        $conn = $this->getConnection();
        $qb   = $this->getMarkersForLocalQb($params);
        $stmt = $conn->executeQuery($qb->getSQL());
        $rows = $stmt->fetchAll();

        $markers = array();
        foreach ($rows as $row) {
            $marker  = new Marker($row['ID'], $row['SUMMARY'], $row['LATITUDE'], $row['LONGITUDE'], 1);
            $markers = array_merge($markers, $marker->toArray());
        }
        return $markers;
    }
}