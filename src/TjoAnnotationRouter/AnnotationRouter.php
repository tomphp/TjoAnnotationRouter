<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouter;

use ArrayObject;
use Zend\Code\Reflection\ClassReflection;
use TjoAnnotationRouter\Config\Merger;
use TjoAnnotationRouter\Parser\ControllerParser;

/**
 * Class for building routing config from annotated controller classes.
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class AnnotationRouter
{
    /**
     * The parser for processing the controller annotations.
     *
     * @var ControllerParser
     */
    protected $parser;

    /**
     * Merges the new config with the existing config.
     *
     * @var Merger
     */
    protected $merger;

    /**
     * Inject required objects.
     *
     * @param ControllerParser $parser
     * @param Merger           $merger
     */
    public function __construct(
        ControllerParser $parser,
        Merger $merger
    ) {
        $this->parser = $parser;
        $this->merger = $merger;
    }

    /**
     * Parses the route annotations and updates the route stack.
     *
     * @param  array $controllers A list of annotated controllers to process.
     * @param  array $config The current routing config.
     * @return void
     */
    public function updateRouteConfig(array $controllers, array &$config)
    {
        $routeList = new ArrayObject();

        foreach ($controllers as $controller) {
            $this->parser->parseReflectedController(new ClassReflection($controller), $routeList);
        }

        if (!sizeof($routeList)) {
            return;
        }

        if (!isset($config['routes'])) {
            $config['routes'] = array();
        }

        $this->merger->merge($routeList->getArrayCopy(), $config['routes']);
    }
}
