<?php

namespace TjoAnnotationRouter\Service;

use TjoAnnotationRouter\Config\Merger;
use TjoAnnotationRouter\Parser\ControllerParser;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for creating and initialising the {@see AnnotationRouter}
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class ControllerParserFactory implements FactoryInterface
{
    /**
     * Create an instance of {@see AnnotationRouter}
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return AnnotationRouter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ControllerParser(
            $serviceLocator->get('TjoAnnotationRouter\AnnotationManager')
        );
    }
}
