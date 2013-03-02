<?php

namespace TjoAnnotationRouter;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Zend Framework 2 module class for the TjoAnnotationRouter module.
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class Module implements
    BootstrapListenerInterface,
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     *
     * @param EventInterface $e
     */
    public function onBootstrap(EventInterface $e)
    {
        $serviceLocator = $e->getApplication()->getServiceManager();

        $config = $serviceLocator->get('Config');

        $routes = $serviceLocator->get('Router')->getRoutes();

        $annotationRouter = $serviceLocator->get('TjoAnnotationRouter\AnnotationRouter');
        $routeConfig = $annotationRouter->updateRoutes(
            $config['tjo_annotation_router']['controllers'],
            $routes
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return array(
            /*
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php',
            ),
            */
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'TjoAnnotationRouter\AnnotationManager' => 'TjoAnnotationRouter\Service\AnnotationManagerFactory',
                'TjoAnnotationRouter\AnnotationRouter'  => 'TjoAnnotationRouter\Service\AnnotationRouterFactory',
            ),
        );
    }
}
