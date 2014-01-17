<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'db'              => array(
        'driver'     => 'Oci8',
        'connection' => 'test.insee.pl/PDBORCL',
        'username'   => 'SPDB',
        'password'   => 'SPDB',
        'charset'    => 'AL32UTF8',
    ),
    'doctrine'        => array(
        'driver'        => array(
            'application' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    APPLICATION_PATH . '/module/Application/src/Application/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'Application\Entity' => 'application'
                )
            ),
        ),
        'configuration' => array(
            'orm_default' => array(
                'naming_strategy' => 'Doctrine\ORM\Mapping\UnderscoreNamingStrategy',
            ),
        ),
        'connection'    => array(
            // default connection name
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\OCI8\Driver',
                'params'      => array(
                    'host'     => '', //override to work with oci8connect
                    'user'     => 'SPDB',
                    'password' => 'SPDB',
                    'dbname'   => 'test.insee.pl/PDBORCL',
                    'service'  => true,
                    'charset'  => 'AL32UTF8',
                )
            )
        ),
    ),
    'event_manager' => array(
        'orm_default' => array(
            'subscribers' => array('DoctrineDBALEventListenersOracleSessionInit')
        )
    ),
    'service_manager' => array(
        'factories'  => array(
            'db' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
        'invokables' => array(
            'Doctrine\ORM\Mapping\UnderscoreNamingStrategy' => 'Doctrine\ORM\Mapping\UnderscoreNamingStrategy',
        ),
    ),
);
