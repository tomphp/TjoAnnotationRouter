<?php

namespace TjoAnnotationRouter;

use ArrayObject;
use Zend\Code\Annotation\AnnotationCollection;
use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Annotation\Parser;
use Zend\Code\Reflection\ClassReflection;

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
     * @var array
     */
    protected $defaultAnnotations = array(
        'Base',
        'Constraint',
        'Controller',
        'DefaultValue',
        'Route',
    );

    /**
     * @var string
     */
    protected $baseName = null;

    /**
     * @var string
     */
    protected $controllerName = null;

    /**
     * Set annotation manager to use when parsing router annotations
     *
     * @param  AnnotationManager $annotationManager
     * @return AnnotationBuilder
     */
    public function setAnnotationManager(AnnotationManager $annotationManager)
    {
        $parser = new Parser\DoctrineAnnotationParser();
        foreach ($this->defaultAnnotations as $annotationName) {
            $class = __NAMESPACE__ . '\\Annotation\\' . $annotationName;
            $parser->registerAnnotation($class);
        }
        $annotationManager->attach($parser);
        $this->annotationManager = $annotationManager;
        return $this;
    }

    /**
     * Retrieve annotation manager
     *
     * If none is currently set, creates one with default annotations.
     *
     * @return AnnotationManager
     */
    public function getAnnotationManager()
    {
        if ($this->annotationManager) {
            return $this->annotationManager;
        }

        $this->setAnnotationManager(new AnnotationManager());
        return $this->annotationManager;
    }

    /**
     * Gets the settings from the controller annotations.
     *
     * @param  string               $controllerName
     * @param  AnnotationCollection $annotations
     * @return void
     */
    protected function parseControllerAnnotations($controllerName, AnnotationCollection $annotations)
    {
        $this->baseName = null;
        $this->baseRoute = null;
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
                    $controllerName
                )
            );
        }
    }

    /**
     * Read the method annotations and construct the config.
     *
     * @param  string               $name
     * @param  AnnotationCollection $annotations
     * @return void
     */
    protected function parseMethodAnnotations(
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
            if (!isset($settings['type'])) {
                $settings['type'] = 'literal';
                // @todo Make it so that route tree parts are customisable
                $settings['options']['route'] = '/' . $part;
                $settings['may_terminate'] = true;
            }

            $part = current($routeParts);

            $settings = &$settings['child_routes'][$part];
        } while (next($routeParts));

        return $settings;
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
            $this->parseControllerAnnotations($controller, $annotations);
        }

        foreach ($reflection->getMethods() as $method) {
            $annotations = $method->getAnnotations($annotationManager);

            $this->parseMethodAnnotations($method->getName(), $annotations, $config);
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
