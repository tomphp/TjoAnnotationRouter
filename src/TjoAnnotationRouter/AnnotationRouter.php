<?php

namespace TjoAnnotationRouter;

use ArrayObject;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Reflection\ClassReflection;
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
     * Retrieve annotation manager
     *
     * If none is currently set, creates one with default annotations.
     *
     * @return AnnotationManager
     */
    protected function getAnnotationManager()
    {
        return $this->annotationManager;
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
        $annotationManager = $this->getAnnotationManager();

        $reflection  = new ClassReflection($controller);

        $annotations = $reflection->getAnnotations($annotationManager);

        if ($annotations instanceof AnnotationCollection) {
            $this->parser->setController($controller, $annotations);
        }

        foreach ($reflection->getMethods() as $method) {
            $annotations = $method->getAnnotations($annotationManager);

            $this->parser->parseMethod($method->getName(), $annotations, $config);
        }
    }

    /**
     * Returns a router config array from the annotations.
     *
     * @param  string $controllers A list of annotated controllers to process.
     * @return array
     */
    public function getRouterConfig(array $controllers)
    {
        $config = new ArrayObject();

        foreach ($controllers as $controller) {
            $this->parseController($controller, $config);
        }

        return $config->getArrayCopy();
    }
}
