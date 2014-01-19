<?php
/**
 * Created by m.swietochowski
 */

namespace Application\Repository;

use Doctrine\DBAL\Connection;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class AbstractRepository implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @return AbstractRepository
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        if (!$this->connection) {
            $this->setConnection($this->getServiceLocator()->get('doctrine.connection.orm_default'));
        }
        return $this->connection;
    }
}