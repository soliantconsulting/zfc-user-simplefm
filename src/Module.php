<?php

namespace Soliant\ZfcUserSimpleFM;

use Zend\ModuleManager\ModuleManager;
use Zend\EventManager\EventManager;
use Soliant\SimpleFM;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->setAllowOverride(true);
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'zfcuser_register_form_hydrator'    => 'Zend\Stdlib\Hydrator\ArraySerializable',
                'Zend\Stdlib\Hydrator\ArraySerializable' => 'Zend\Stdlib\Hydrator\ArraySerializable',
                'Soliant\ZfcUserSimpleFM\Authentication\Adapter\SimpleFM' => 'Soliant\ZfcUserSimpleFM\Authentication\Adapter\SimpleFM',
            ),
            'factories' => array(
                'zfcuser_user_hydrator' => 'Soliant\ZfcUserSimpleFM\Factory\Mapper\UserHydratorFactory',
                'zfcuser_user_mapper' => function ($sm)
                {
                    $options = $sm->get('zfcuser_module_options');
                    $mapper = new Mapper\User();
                    $mapper->setDbAdapter($sm->get('simplefm'));
                    $mapper->setHydrator($sm->get('zfcuser_register_form_hydrator'));
                    $entityClass = $options->getUserEntityClass();
                    $mapper->setEntityPrototype(new $entityClass);
                    $mapper->setTableName($options->getTableName());
                    $mapper->init(); # work around for constuctor params in ancestor

                    return $mapper;
                },
            ),
        );
    }
}
