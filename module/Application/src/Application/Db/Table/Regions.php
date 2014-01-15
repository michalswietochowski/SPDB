<?php
/**
 * Created by m.swietochowski
 */

namespace Application\Db\Table;

use Application\Gis\Shapefile;
use Zend\Db\Sql;
use Zend\Db\TableGateway\Feature;
use Zend\Db\TableGateway\TableGateway;

class Regions extends TableGateway
{
    /**
     * @var string
     */
    protected $importDir;

    public function __construct()
    {
        $this->table      = 'issues';
        $this->featureSet = new Feature\FeatureSet();
        $this->featureSet->addFeature(new Feature\GlobalAdapterFeature());
        $this->importDir = realpath(APPLICATION_PATH . '/data/import/');
        $this->initialize();
    }

    /**
     * Imports data from CSV
     * @param string $filename
     * @param string $level
     * @return bool
     */
    public function importData($filename, $level)
    {
        $file = $this->importDir . '/' . $filename;

        echo '<pre style="margin:60px">';
        if (is_readable($file)) {
            $shpParser = new Shapefile\Parser();
            $shpParser->load($file);
            echo 1;
        }
        echo '</pre>';

        return false;
    }

    /**
     * Imports USA administration level 0 (country)
     * @return bool
     */
    public function importUsaLevelCountry()
    {
        return $this->importData('USA_adm/USA_adm0.shp', 0);
    }

    /**
     * Imports USA administration level 1 (states)
     * @return bool
     */
    public function importUsaLevelStates()
    {
        return $this->importData('USA_adm/USA_adm1.shp', 0);
    }

    /**
     * Imports USA administration level 2 (counties)
     * @return bool
     */
    public function importUsaLevelCounties()
    {
        return $this->importData('USA_adm/USA_adm2.shp', 0);
    }
}