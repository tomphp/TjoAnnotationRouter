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
        unset($newRoute['child_routes']);

        if ($routeInfo['type']) {
            return $routeInfo;
        }

        $newRoute = array(
            'type'          => 'Literal',
            'may_terminate' => false,
            'options'       => array(
                'route' => $routeName, // @todo Allow customisation of intermediate route names.
            ),
        );
    }

    /**
     * Recursively update the route stack.
     *
     * @param  ArrayObject  $routeList
     * @param  PriorityList $parent
     * @return RouteInterface
     */
    protected function recursiveUpdateRoutes(ArrayObject $routeList, PriorityList $parent)
    {
        foreach ($routeList as $routeName => $routeInfo) {
            if (!$parent->get($routeName)) {
                #continue;
                //$parent->insert($this->newRoute($routeName, $routeInfo));
            }

            #$route = $parent->get($routeName);
            #var_dump($route);

            /*
            if (isset($routeInfo['child_routes'])) {
                $this->recursiveUpdateRoutes($routeInfo['child_routes'], $route);
            }
            */
        }
    }

    /**
     * Parses the route annotations and updates the route stack.
     *
     * @param  string       $controllers A list of annotated controllers to process.
     * @param  PriorityList $routes The current routing stack.
     * @return void
     */
    public function updateRoutes(array $controllers, PriorityList $routes)
    {
        $routeList = new ArrayObject();

        foreach ($controllers as $controller) {
            $this->parseController($controller, $routeList);
        }

        echo "<pre>";
        //print_r($routeList);

        $this->recursiveUpdateRoutes($routeList, $routes);

        foreach ($routes as $routeName => $route) {
            echo "ROUTE = " . $routeName . "<br>";
        }

        echo "</pre>";

        exit();

        // @todo update the routes with the new config
    }
}
