<?php

namespace ZfcUserSimpleFM;

use Zend\Validator\InArray;

use Zend\ModuleManager\ModuleManager;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\Mvc\MvcEvent;
use SimpleFMAuth\Mapper;
use Soliant\SimpleFM;

class Module implements EventManagerAwareInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'simplefm' => function($sm)
                {
                    $config = $sm->get('Config');
                    $adapter = new SimpleFM\Adapter($config['simple_fm_host_params']);

                    return $adapter;
                },
                'zfcuser_user_hydrator' => function ($sm) {
                    $hydrator = new \Zend\Stdlib\Hydrator\ArraySerializable();
                    return $hydrator;
                },
                'zfcuser_user_mapper' => function ($sm) {
                    $options = $sm->get('zfcuser_module_options');
                    $mapper = new Mapper\User();
                    $mapper->setDbAdapter($sm->get('simplefm'));
                    $entityClass = $options->getUserEntityClass();
                    $mapper->setEntityPrototype(new $entityClass);
                    $mapper->setHydrator($sm->get('zfcuser_user_hydrator'));
                    $mapper->setTableName($options->getTableName());
                    return $mapper;
                },
                'sfm_validation_adapter' => function ($sm) {
                    $config = $sm->get('config');
                    $hostParams = $config['sfm_auth']['simple_fm_host_params'];
                    $dbAdapter = new \Soliant\SimpleFM\Adapter($hostParams);
                    return $dbAdapter;
                },
                'sfm_auth_adapter' => function ($sm) {
                    $config = $sm->get('config');
                    $authConfig = $config['sfm_auth']['sfm_auth_params'];
                    $validateSimpleFmAdapter = $sm->get('sfm_validation_adapter');
                    return new \Soliant\SimpleFM\ZF2\Authentication\Adapter\SimpleFM($authConfig, $validateSimpleFmAdapter);
                },
                'sfm_auth_storage' => function ($sm) {
                    $config = $sm->get('config');
                    $namespace = $config['sfm_auth']['sfm_session_namespace'];
                    return new \Soliant\SimpleFM\ZF2\Authentication\Storage\Session($namespace);
                },
                'sfm_auth_service' => function ($sm) {
                    $storage = $sm->get('sfm_auth_storage');
                    return new \Zend\Authentication\AuthenticationService($storage);
                },
            ),
        );
    }
}
