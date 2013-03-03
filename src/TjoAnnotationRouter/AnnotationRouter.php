<?php

namespace TjoAnnotationRouter;

use ArrayObject;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Reflection\ClassReflection;
use Zend\Mvc\Router\PriorityList;
use TjoAnnotationRouter\Config\Merger;
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
     * @var Merger
     */
    protected $merger;

    /**
     * @param AnnotationManager $annotationManager
     * @param ControllerParser  $parser
     */
    public function __construct(
        AnnotationManager $annotationManager,
        ControllerParser $parser,
        Merger $merger
    ) {
        $this->annotationManager = $annotationManager;
        $this->parser = $parser;
        $this->merger = $merger;
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
            $this->parser->setController($annotations);
        }

        foreach ($reflection->getMethods() as $method) {
            $annotations = $method->getAnnotations($this->annotationManager);

            $this->parser->parseMethod($method->getName(), $annotations, $config);
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

        $this->merger->merge($routeList->getArrayCopy(), $config['routes']);
    }
}
