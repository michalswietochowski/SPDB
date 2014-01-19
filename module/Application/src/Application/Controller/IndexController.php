<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }

    public function getMarkersAction()
    {
        $jsonModel  = new JsonModel();
        $services   = $this->getServiceLocator();
        $repository = $services->get('Application\Repository\Issue');

        $params  = $this->params()->fromPost();
        $markers = $repository->getMarkers($params);
        $sql     = $repository->getSql($params);
        $jsonModel->setVariables(compact('sql', 'markers'));
        return $jsonModel;
    }
}
