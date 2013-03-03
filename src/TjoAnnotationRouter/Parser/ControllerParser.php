<?php

namespace TjoAnnotationRouter\Parser;

use ArrayObject;
use TjoAnnotationRouter\Annotation;
use TjoAnnotationRouter\Exception;
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
     * The name of the controller
     *
     * @var string
     */
    protected $controllerName;

    /**
     * The name that all routes for this controller will exist under.
     *
     * @var string
     */
    protected $baseName = null;

    /**
     * Inject the required objects.
     *
     * @param AnnotationManager $annotationManager
     */
    public function __construct(AnnotationManager $annotationManager)
    {
        $this->annotationManager = $annotationManager;
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
            $this->setController($annotations);
        }

        foreach ($reflection->getMethods() as $method) {
            $annotations = $method->getAnnotations($this->annotationManager);

            $this->parseMethod($method->getName(), $annotations, $config);
        }
    }

    /**
     * Set the name of the controller and parse the class annotations.
     *
     * @param  AnnotationCollection $annotations
     * @return void
     * @throws Exception\DomainException
     */
    protected function setController(AnnotationCollection $annotations)
    {
        $this->baseName = null;
        $this->controllerName = null;

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Annotation\Controller) {
                $this->controllerName = $annotation->getName();
            } elseif ($annotation instanceof Annotation\Base) {
                $this->baseName = $annotation->getName();
            }
        }

        if (null === $this->controllerName) {
            throw new Exception\DomainException(
                sprintf(
                    'Controller %s requires a Controller annotation',
                    $this->controllerName
                )
            );
        }
    }

    /**
     * Read the method annotations and construct the config.
     *
     * @param  string               $name
     * @param  AnnotationCollection $annotations
     * @param  ArrayObject          $config
     * @return void
     */
    protected function parseMethod(
        $name,
        AnnotationCollection $annotations,
        ArrayObject $config
    ) {
        if (!preg_match('/^(.*)Action$/i', $name, $match)) {
            return;
        }

        $actionName = $match[1];

        $routeType = null;
        $routeName = null;
        $routeRoute = null;
        $defaults = array();
        $constraints = array();

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Annotation\Route) {
                $routeType = $annotation->getType();
                $routeName = $annotation->getName();
                $routeRoute = $annotation->getRoute();
            } elseif ($annotation instanceof Annotation\DefaultValue) {
                $defaults[$annotation->getParam()] = $annotation->getValue();
            } elseif ($annotation instanceof Annotation\Constraint) {
                $constraints[$annotation->getParam()] = $annotation->getRule();
            }
        }

        if (null === $routeType) {
            return;
        }

        // @todo Verify all required values are set

        $defaults['action'] = $actionName;
        $defaults['controller'] = $this->controllerName;

        if ($routeName[0] !== '/' && null !== $this->baseName) {
            $routeName = $this->baseName . '/' . $routeName;
        }

        $settings = &$this->findConfigSection($routeName, $config);

        $settings['type'] = $routeType;

        $settings['options'] = array(
                'route' => $routeRoute,
                'constraints' => $constraints,
                'defaults' => $defaults,
        );
    }

    /**
     * Navigate through the config try to find the given route leaf.
     *
     * @param  string $routeName
     * @param  ArrayObject $config
     * @return array
     */
    protected function &findConfigSection($routeName, ArrayObject $config)
    {
        $routeParts = explode('/', $routeName);

        reset($routeParts);

        $part = current($routeParts);

        $settings = &$config[$part];

        if (!next($routeParts)) {
            return $settings;
        }

        do {
            $part = current($routeParts);

            $settings = &$settings['child_routes'][$part];
        } while (next($routeParts));

        return $settings;
    }
}
