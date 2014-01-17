<?php

namespace Application\Controller;

use Application\Db\Table;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;

class ImportController extends AbstractActionController
{
    public function indexAction()
    {
        $issuesTable = new Table\Issues();
        $issuesTable->importTestData();
        $issuesTable->importTrainData();
    }

    public function testAction()
    {
        /**
         * @var \Doctrine\ORM\EntityManager
         */
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $entity = $em->find('Application\Entity\State', 66);
        var_dump($entity);
        $entity = $em->find('Application\Entity\County', 53067);
        var_dump($entity);
        return false;
    }
}

