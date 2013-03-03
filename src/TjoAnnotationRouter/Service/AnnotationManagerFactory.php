<?php

namespace TjoAnnotationRouter\Service;

use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Annotation\Parser\DoctrineAnnotationParser;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating an instance of the Zend AnnotationManager
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class AnnotationManagerFactory implements FactoryInterface
{
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
     * Returns a configured instance of Zend\Code\Annotation\AnnotationManager
     *
     * @return AnnotationManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $annotationManager = new AnnotationManager();

        $parser = new DoctrineAnnotationParser();

        foreach ($this->defaultAnnotations as $annotationName) {
            $class = 'TjoAnnotationRouter\\Annotation\\' . $annotationName;
            $parser->registerAnnotation($class);
        }

        $annotationManager->attach($parser);

        return $annotationManager;
    }
}
