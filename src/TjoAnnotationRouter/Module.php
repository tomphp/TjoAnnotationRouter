<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouter;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Zend Framework 2 module class for the TjoAnnotationRouter module.
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
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
                'TjoAnnotationRouter\AnnotationManager'       => 'TjoAnnotationRouter\Service\AnnotationManagerFactory',
                'TjoAnnotationRouter\AnnotationRouter'        => 'TjoAnnotationRouter\Service\AnnotationRouterFactory',
                'TjoAnnotationRouter\Parser\ControllerParser' => 'TjoAnnotationRouter\Service\ControllerParserFactory',
                // Override the built in zf2 router factory
                'Router'                                      => 'TjoAnnotationRouter\Service\RouterFactory',
            ),
        );
    }
}
