<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouter\Parser;

use ArrayObject;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Reflection\ClassReflection;

/**
 * Parses the routing annotations for a given controller into Router config.
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class ControllerParser
{
    /**
     * The ZF2 annotation manager.
     *
     * @var AnnotationManager
     */
    protected $annotationManager;

    /**
     * Processes the annotations for the controller.
     *
     * @var ControllerAnnotationProcessor
     */
    protected $processor;

    /**
     * Inject the required objects.
     *
     * @param AnnotationManager $annotationManager
     */
    public function __construct(
        AnnotationManager $annotationManager,
        ControllerAnnotationProcessor $processor
    ) {
        $this->annotationManager = $annotationManager;
        $this->processor = $processor;
    }

    /**
     * Returns a reflection of the given controller.
     *
     * @param  mixed $controller Controller name or instance.
     * @return void
     */
    public function getReflectedController($controller)
    {
        return new ClassReflection($controller);
    }

    /**
     * Builds the config for a controller.
     *
     * @param  string          $name
     * @param  ClassReflection $reflection
     * @param  ArrayObject     $config
     * @return ArrayObject     Returns the config array object.
     */
    public function parseReflectedController($name, ClassReflection $reflection, ArrayObject $config)
    {
        $annotations = $reflection->getAnnotations($this->annotationManager);

        if ($annotations instanceof AnnotationCollection) {
            $this->processor->processController($name, $annotations);
        }

        foreach ($reflection->getMethods() as $method) {
            $annotations = $method->getAnnotations($this->annotationManager);

            if (!$annotations instanceof AnnotationCollection) {
                continue;
            }

            $this->processor->processMethod($method->getName(), $annotations, $config);
        }

        return $config;
    }
}
