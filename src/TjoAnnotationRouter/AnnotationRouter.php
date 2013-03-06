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
use TjoAnnotationRouter\Options\Config;
use TjoAnnotationRouter\Parser\ControllerParser;

/**
 * Class for building routing config from annotated controller classes.
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class AnnotationRouter
{
    /**
     * The module config.
     *
     * @var Config
     */
    protected $config;

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
     * @param Config           $config
     * @param ControllerParser $parser
     * @param Merger           $merger
     */
    public function __construct(
        Config $config,
        ControllerParser $parser,
        Merger $merger
    ) {
        $this->config = $config;
        $this->parser = $parser;
        $this->merger = $merger;
    }

    /**
     * Attempt to use cache rather than annotations.
     *
     * @todo Should file_exists be called here or is there some ZF mechanism for this?
     * @param  array $config
     * @return boolean True if cached config was found.
     */
    protected function loadCachedConfig(array &$config)
    {
        $cacheFilePath = $this->config->getCacheFile();

        if (!file_exists($cacheFilePath)) {
            return false;
        }

        $cachedConfig = include $cacheFilePath;

        if (!is_array($cachedConfig) || !sizeof($cachedConfig)) {
            return false;
        }

        if (!isset($config['routes'])) {
            $config['routes'] = array();
        }

        $this->merger->merge($cachedConfig, $config['routes']);

        return true;
    }

    /**
     * Returns the config fetched from the annotations
     *
     * @return array
     */
    public function getRouteConfig()
    {
        $routeList = new ArrayObject();

        foreach ($this->config->getControllers() as $controller) {
            $this->parser->parseReflectedController(new ClassReflection($controller), $routeList);
        }

        return $routeList->getArrayCopy();
    }

    /**
     * Parses the route annotations and updates the route stack.
     *
     * @param  array $config The current routing config.
     * @return void
     */
    public function updateRouteConfig(array &$config)
    {
        if ($this->loadCachedConfig($config)) {
            return;
        }

        $routeList = $this->getRouteConfig();

        if (!sizeof($routeList)) {
            return;
        }

        if (!isset($config['routes'])) {
            $config['routes'] = array();
        }

        $this->merger->merge($routeList, $config['routes']);
    }
}
