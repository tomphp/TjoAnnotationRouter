<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouter\Config;

/**
 * This class provides the methods to merge 2 sets of router config together.
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class Merger
{
    /**
     * Compile the information for a new route.
     *
     * @param  string $routeName
     * @param  array  $routeInfo
     * @return array
     */
    protected function newRoute($routeName, array $routeInfo)
    {
        unset($routeInfo['child_routes']);

        if (isset($routeInfo['type'])) {
            return $routeInfo;
        }

        return array(
            'type'          => 'Literal',
            'may_terminate' => false,
            'options'       => array(
                'route' => '/' . $routeName, // @todo Allow customisation of intermediate route names.
            ),
        );
    }

    /**
     * Recursively update the route stack.
     *
     * @todo Convert recursion to iteration
     * @param  array $newConfig
     * @param  array $config
     * @return RouteInterface
     */
    public function merge(array $newConfig, array &$config)
    {
        foreach ($newConfig as $routeName => $routeInfo) {
            if (!isset($config[$routeName])) {
                $config[$routeName] = $this->newRoute($routeName, $routeInfo);
            }

            if (isset($routeInfo['child_routes'])) {
                if (!isset($config[$routeName]['child_routes'])) {
                    $config[$routeName]['child_routes'] = array();
                }

                $this->merge($routeInfo['child_routes'], $config[$routeName]['child_routes']);
            }
        }
    }
}
