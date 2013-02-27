<?php

namespace TjoAnnotationRouter\Annotation;

/**
 * Class for a Route annotation.
 *
 * @Annotation
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class Route extends AbstractMultiParamRoute
{
    /**
     * @var string
     */
    protected $type = 'literal';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $route;

    /**
     * Receive and process the contents of an annotation
     *
     * @param  array $data
     * @throws Exception\DomainException
     */
    public function __construct(array $data)
    {
        $this->setParam($data, 'type', false);
        $this->setParam($data, 'name');
        $this->setParam($data, 'route');
    }

    /**
     * Retrieve the route type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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

    /**
     * Return the route.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }
}
