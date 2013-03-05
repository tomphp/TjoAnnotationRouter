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
     * Builds the config for a controller.
     *
     * @param  ClassReflection $reflection
     * @param  ArrayObject     $config
     * @return void
     */
    public function parseReflectedController(ClassReflection $reflection, ArrayObject $config)
    {
        $annotations = $reflection->getAnnotations($this->annotationManager);

        if ($annotations instanceof AnnotationCollection) {
            $this->processor->processController($annotations);
        }

        foreach ($reflection->getMethods() as $method) {
            $annotations = $method->getAnnotations($this->annotationManager);

            if (!$annotations instanceof AnnotationCollection) {
                continue;
            }

            $this->processor->processMethod($method->getName(), $annotations, $config);
        }
    }
}
