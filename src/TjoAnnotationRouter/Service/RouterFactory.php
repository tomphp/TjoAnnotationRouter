<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouter\Service;

use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Mvc\Router\Console\SimpleRouteStack as ConsoleRouter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Console\Console;

/**
 * This is a modified version {@see Zend\Mvc\Service\RouterFactory} which
 * adds the extra annotated config to the router.
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class RouterFactory implements FactoryInterface
{
    /**
     * Create and return the router
     *
     * Retrieves the "router" key of the Config service, and uses it
     * to instantiate the router. Uses the TreeRouteStack implementation by
     * default.
     *
     * @param  ServiceLocatorInterface        $serviceLocator
     * @param  string|null                     $cName
     * @param  string|null                     $rName
     * @return \Zend\Mvc\Router\RouteStackInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $cName = null, $rName = null)
    {
        $config             = $serviceLocator->has('Config') ? $serviceLocator->get('Config') : array();

        // Defaults
        $routerClass        = 'Zend\Mvc\Router\Http\TreeRouteStack';
        $routerConfig       = isset($config['router']) ? $config['router'] : array();

        // Console environment?
        if ($rName === 'ConsoleRouter'                       // force console router
            || ($cName === 'router' && Console::isConsole()) // auto detect console
        ) {
            // We are in a console, use console router defaults.
            $routerClass = 'Zend\Mvc\Router\Console\SimpleRouteStack';
            $routerConfig = isset($config['console']['router']) ? $config['console']['router'] : array();
        }

        // Add the extra annotation router config
        $annotationRouter = $serviceLocator->get('TjoAnnotationRouter\AnnotationRouter');

        $annotationRouter->updateRouteConfig($routerConfig);

        // Obtain the configured router class, if any
        if (isset($routerConfig['router_class']) && class_exists($routerConfig['router_class'])) {
            $routerClass = $routerConfig['router_class'];
        }

        // Inject the route plugins
        if (!isset($routerConfig['route_plugins'])) {
            $routePluginManager = $serviceLocator->get('RoutePluginManager');
            $routerConfig['route_plugins'] = $routePluginManager;
        }

        // Obtain an instance
        $factory = sprintf('%s::factory', $routerClass);
        return call_user_func($factory, $routerConfig);
    }
}
