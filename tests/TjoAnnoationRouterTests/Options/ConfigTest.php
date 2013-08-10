<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouterTests\Options;

use TjoAnnotationRouter\Options\Config;

/**
 * Tests for {@see Config}.
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the config is read and parsed properly.
     *
     * @cover TjoAnnotationRouter\Options\Config::__construct
     * @cover TjoAnnotationRouter\Options\Config::getCacheFile
     * @cover TjoAnnotationRouter\Options\Config::setCacheFile
     */
    public function testConfig()
    {
        $testConfig = array(
            'cache_file'  => '/some/random/path',
        );

        $config = new Config($testConfig);

        $this->assertEquals(
            $testConfig['cache_file'],
            $config->getCacheFile(),
            'Asserting cache_value option is correct.'
        );
    }
}
