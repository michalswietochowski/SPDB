<?php
/**
 * Created by m.swietochowski
 */

namespace Application\Db\Table;

use Zend\Db\Sql;
use Zend\Db\TableGateway\Feature;
use Zend\Db\TableGateway\TableGateway;

class Issues extends TableGateway
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
     * @param string $type (train or test)
     * @return bool
     */
    public function importData($filename, $type)
    {
        $file = $this->importDir . '/' . $filename;

        if (($handle = fopen($file, 'r')) !== false) {
            //disable time limit
            set_time_limit(0);

            $columns = null;
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$columns) {
                    //save first row as column names and skip
                    $columns = $data;
                    continue;
                }

                $rowData = array_combine($columns, $data);

                //skip if geometry data is invalid
                if (!is_numeric($rowData['longitude']) || !is_numeric($rowData['latitude'])) {
                    continue;
                }

                if (empty($rowData['description'])) {
                    $rowData['description'] = null;
                }
                if ($rowData['source'] = 'NA') {
                    $rowData['source'] = null;
                }
                if ($rowData['tag_type'] = 'NA') {
                    $rowData['tag_type'] = null;
                }

                $rowData['type']     = $type;
                $rowData['geometry'] = new Sql\Expression('Point(?, ?)', array(
                    $rowData['longitude'],
                    $rowData['latitude'],
                ));

                $this->insert($rowData);
            }
            fclose($handle);
            return true;
        }
        return false;
    }

    /**
     * Imports test data
     * @return bool
     */
    public function importTestData()
    {
        return $this->importData('test.csv', 'test');
    }

    /**
     * Imports train data
     * @return bool
     */
    public function importTrainData()
    {
        return $this->importData('train.csv', 'train');
    }
}