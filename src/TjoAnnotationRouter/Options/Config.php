<?php

namespace TjoAnnotationRouter\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class for providing the module options.
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class Config extends AbstractOptions
{
    /**
     * The path to the cache file.
     *
     * @var string
     */
    protected $cacheFile;

    /**
     * The list of annotated controllers.
     * @var array
     */
    protected $controllers = array();
    
    /**
     * Gets the value for cacheFile.
     *
     * @return string
     */
    public function getCacheFile()
    {
        return $this->cacheFile;
    }

    /**
     * Sets the value for cacheFile.
     *
     * @param  string $cacheFile
     * @return self
     */
    public function setCacheFile($cacheFile)
    {
        $this->cacheFile = (string) $cacheFile;

        return $this;
    }
    
    /**
     * Gets the value for controllers.
     *
     * @return array
     */
    public function getControllers()
    {
        return $this->controllers;
    }
    
    /**
     * Sets the value for controllers.
     *
     * @param  array $controllers
     * @return self
     */
    public function setControllers(array $controllers)
    {
        $this->controllers = $controllers;

        return $this;
    }
}
