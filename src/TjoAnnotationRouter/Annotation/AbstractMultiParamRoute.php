<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouter\Annotation;

/**
 * Class for an annotation which takes multiple parameters.
 *
 * @Annotation
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
abstract class AbstractMultiParamRoute
{
    /**
     * Sets a property from the $data array with validation.
     *
     * @param  array  $data
     * @param  string $name
     * @param  bool   $required
     * @param  string $type
     * @throws Exception\DomainException
     */
    protected function setParam(array $data, $name, $required = true, $type = 'string')
    {
        if (!isset($data[$name]) || ($required && gettype($data[$name]) !== $type)) {
            throw new Exception\DomainException(
                sprintf(
                    '%s expects the annotation %s parameter to define a %s; received "%s"',
                    get_class($this),
                    $name,
                    $type,
                    gettype($data[$name])
                )
            );
        }

        $this->$name = $data[$name];
    }
}
