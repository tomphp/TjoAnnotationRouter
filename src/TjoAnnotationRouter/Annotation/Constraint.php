<?php

namespace TjoAnnotationRouter\Annotation;

/**
 * Class for a Base annotation.
 *
 * @Annotation
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class Constraint extends AbstractMultiParamRoute
{
    /**
     * @var string
     */
    protected $param;

    /**
     * @var string
     */
    protected $rule;

    /**
     * Receive and process the contents of an annotation
     *
     * @param  array $data
     * @throws Exception\DomainException
     */
    public function __construct(array $data)
    {
        $this->setParam($data, 'param');
        $this->setParam($data, 'rule');
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
     * Return the constraint rule.
     *
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }
}
