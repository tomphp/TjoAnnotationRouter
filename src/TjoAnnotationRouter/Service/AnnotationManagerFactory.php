<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

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
