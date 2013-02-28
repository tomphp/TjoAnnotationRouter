<?php

namespace TjoAnnotationRouter\Service;

use TjoAnnotationRouter\AnnotationRouter;
use TjoAnnotationRouter\Parser\ControllerParser;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating and initialising the {@see AnnotationRouter}
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class AnnotationRouterFactory implements FactoryInterface
{
    /**
     * Create an instance of {@see AnnotationRouter}
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return AnnotationRouter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $annotationRouter = new AnnotationRouter();

        $manager = $serviceLocator->get('TjoAnnotationRouter\AnnotationManager');

        $parser = new ControllerParser();

        return $annotationRouter($manager, $parser);
    }
}
