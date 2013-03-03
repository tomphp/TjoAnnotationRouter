<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouter\Service;

use TjoAnnotationRouter\AnnotationRouter;
use TjoAnnotationRouter\Config\Merger;
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
        return new AnnotationRouter(
            $serviceLocator->get('TjoAnnotationRouter\Parser\ControllerParser'),
            new Merger()
        );
    }
}
