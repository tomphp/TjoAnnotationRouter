<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouter\Annotation;

/**
 * Class for a Base annotation.
 *
 * @Annotation
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class Base
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Receive and process the contents of an annotation
     *
     * @param  array $data
     * @throws Exception\DomainException
     */
    public function __construct(array $data)
    {
        if (!isset($data['value']) || !is_string($data['value'])) {
            throw new Exception\DomainException(
                sprintf(
                    '%s expects the annotation parameter to define a string; received "%s"',
                    get_class($this),
                    gettype($data['value'])
                )
            );
        }

        $this->name = $data['value'];
    }

    /**
     * Retrieve the route name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
