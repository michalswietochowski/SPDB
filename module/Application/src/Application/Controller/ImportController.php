<?php

namespace Application\Controller;

use Application\Db\Table;
use Zend\Mvc\Controller\AbstractActionController;

class ImportController extends AbstractActionController
{
    public function indexAction()
    {
        $issuesTable = new Table\Issues();
        $issuesTable->importTestData();
        $issuesTable->importTrainData();
    }
}

