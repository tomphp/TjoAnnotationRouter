<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

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
}
