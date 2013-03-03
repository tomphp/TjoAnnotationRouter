<?php

namespace TjoAnnotationRouter;

use ArrayObject;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Reflection\ClassReflection;
use Zend\Mvc\Router\PriorityList;
use TjoAnnotationRouter\Parser\ControllerParser;

/**
 * Class for building routing config from annotated controller classes.
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class AnnotationRouter
{
    /**
     * @var AnnotationManager
     */
    protected $annotationManager;

    /**
     * @var ControllerParser
     */
    protected $parser;

    /**
     * @param AnnotationManager $annotationManager
     * @param ControllerParser  $parser
     */
    public function __construct(AnnotationManager $annotationManager, ControllerParser $parser)
    {
        $this->annotationManager = $annotationManager;
        $this->parser = $parser;
    }

    /**
     * Builds the config for a controller.
     *
     * @param  string $controller
     * @param  string $config
     * @return void
     */
    protected function parseController($controller, ArrayObject $config)
    {
        $reflection  = new ClassReflection($controller);

        $annotations = $reflection->getAnnotations($this->annotationManager);

        if ($annotations instanceof AnnotationCollection) {
            $this->parser->setController($controller, $annotations);
        }

        foreach ($reflection->getMethods() as $method) {
            $annotations = $method->getAnnotations($this->annotationManager);

            $this->parser->parseMethod($method->getName(), $annotations, $config);
        }
    }

    /**
     * Compile the information for a new route.
     *
     * @param  string $routeName
     * @param  array  $routeInfo
     * @return array
     */
    protected function newRoute($routeName, array $routeInfo)
    {
        unset($routeInfo['child_routes']);

        if (isset($routeInfo['type'])) {
            return $routeInfo;
        }

        return array(
            'type'          => 'Literal',
            'may_terminate' => false,
            'options'       => array(
                'route' => '/' . $routeName, // @todo Allow customisation of intermediate route names.
            ),
        );
    }

    /**
     * Recursively update the route stack.
     *
     * @todo Typehint routeList, something weird is happening.
     * @param  ArrayObject $routeList
     * @param  array       $parent
     * @return RouteInterface
     */
    protected function recursiveUpdateRoutes($routeList, array &$parent)
    {
        foreach ($routeList as $routeName => $routeInfo) {
            if (!isset($parent[$routeName])) {
                $parent[$routeName] = $this->newRoute($routeName, $routeInfo);
            }

            if (isset($routeInfo['child_routes'])) {
                if (!isset($parent[$routeName]['child_routes'])) {
                    $parent[$routeName]['child_routes'] = array();
                }

                $this->recursiveUpdateRoutes($routeInfo['child_routes'], $parent[$routeName]['child_routes']);
            }
        }
    }

    /**
     * Parses the route annotations and updates the route stack.
     *
     * @param  string $controllers A list of annotated controllers to process.
     * @param  array  $config The current routing config.
     * @return void
     */
    public function updateRouteConfig(array $controllers, array &$config)
    {
        $routeList = new ArrayObject();

        foreach ($controllers as $controller) {
            $this->parseController($controller, $routeList);
        }

        if (!sizeof($routeList)) {
            return;
        }

        if (!isset($config['routes'])) {
            $config['routes'] = array();
        }

        $this->recursiveUpdateRoutes($routeList, $config['routes']);
    }
}
