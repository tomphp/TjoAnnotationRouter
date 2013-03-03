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
class DefaultValue extends AbstractMultiParamRoute
{
    /**
     * @var string
     */
    protected $param;

    /**
     * @var string
     */
    protected $value;

    /**
     * Receive and process the contents of an annotation
     *
     * @param  array $data
     * @throws Exception\DomainException
     */
    public function __construct(array $data)
    {
        $this->setParam($data, 'param');
        $this->setParam($data, 'value');
    }

    /**
     * Retrieve the parameter name.
     *
     * @return string
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * Return the value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
