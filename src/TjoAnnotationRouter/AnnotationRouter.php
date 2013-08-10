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
use Zend\Mvc\Controller\ControllerManager;

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
     * This is used to fetch a list of all controllers in the application.
     *
     * @var ControllerManager
     */
    protected $controllerManager;

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
        Merger $merger,
        ControllerManager $controllerManager
    ) {
        $this->config            = $config;
        $this->parser            = $parser;
        $this->merger            = $merger;
        $this->controllerManager = $controllerManager;
    }

    /**
     * Returns a list of controller names and class names.
     *
     * @return array
     */
    public function controllerList()
    {
        $names = $this->controllerManager->getCanonicalNames();

        $controllers = array();

        foreach ($names as $name) {
            try {
                $controllers[$name] = $this->controllerManager->get($name);
            } catch (\Exception $e) {
                continue;
            }
        }

        return $controllers;
    }

    /**
     * Attempt to use cache rather than annotations.
     *
     * @todo Should file_exists be called here or is there some ZF mechanism for this?
     * @param  array $config
     * @return boolean True if cached config was found.
     */
    public function loadCachedConfig(array &$config)
    {
        $cacheFilePath = $this->config->getCacheFile();

        if (!file_exists($cacheFilePath)) {
            return false;
        }

        $cachedConfig = include $cacheFilePath;

        if (!is_array($cachedConfig)) {
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
     * @param  array The list of controllers keyed by canonical name to generate config for.
     * @return array
     */
    public function getRouteConfig(array $controllers)
    {
        $routeList = new ArrayObject();

        foreach ($controllers as $name => $controller) {
            $routeList = $this->parser->parseReflectedController(
                $name,
                $this->parser->getReflectedController($controller),
                $routeList
            );
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

        $routeList = $this->getRouteConfig($this->controllerList());

        if (!sizeof($routeList)) {
            return;
        }

        if (!isset($config['routes'])) {
            $config['routes'] = array();
        }

        $this->merger->merge($routeList, $config['routes']);
    }
}
