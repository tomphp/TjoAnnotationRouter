<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouter;

/**
 * Tests for {@see AnnotationRouter}.
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class AnnotationRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The instance we are going to test.
     *
     * @var AnnotationRouter
     */
    protected $router;

    /**
     * 
     * @var \TjoAnnotationRouter\Options\Config
     */
    protected $config;

    /**
     * 
     * @var \TjoAnnotationRouter\Parser\ControllerParser
     */
    protected $parser;

    /**
     * 
     * @var \TjoAnnotationRouter\Config\Merger
     */
    protected $merger;

    /**
     * Prepare an instance to test.
     */
    protected function setUp()
    {
        $this->config = $this->getMockBuilder('TjoAnnotationRouter\Options\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->parser = $this->getMockBuilder('TjoAnnotationRouter\Parser\ControllerParser')
            ->disableOriginalConstructor()
            ->getMock();

        $this->merger = $this->getMock('TjoAnnotationRouter\Config\Merger');

        $this->router = new AnnotationRouter($this->config, $this->parser, $this->merger);
    }

    /**
     * Creates a temporary cache file.
     *
     * @param  array $cache
     * @return string
     */
    protected function createCacheFile(array $cache = null) {
        // Generate a cache file
        $cacheFile = tempnam('/tmp', 'tjo_cache_test');

        if (null !== $cache) {
            $fp = fopen($cacheFile, 'w');
            fputs($fp, '<?php return ' . var_export($cache, true) . ";\n");
            fclose($fp);
        }

        $this->config->expects($this->any())
            ->method('getCacheFile')
            ->will($this->returnValue($cacheFile));

        return $cacheFile;
    }

    /**
     * Data provider for testing the loadCacheFile method.
     *
     * @return array
     */
    public function cacheConfigProvider()
    {
        return array(
            array(
                false,
                'Asserting the cache loader returns false when no cache file found',
                false,
                false
            ),
            array(
                false,
                'Asserting the cache loader returns false when bad cache file found',
                true,
                false
            ),
            array(
                true,
                'Asserting the cache loader returns false when bad cache file found',
                true,
                true,
                array(
                    'blah' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/blah',
                        ),
                    ),
                )
            )
        );
    }

    /**
     * @covers TjoAnnotationRouter\Annotation::loadCacheConfig
     * @dataProvider cacheConfigProvider
     *
     * @param boolean $expected
     * @param string  $message
     * @param boolean $fileExists
     * @param boolean $mergerCalled
     * @param array   $config
     */
    public function testLoadCacheConfig(
        $expected,
        $message,
        $fileExists,
        $mergerCalled,
        array $config = null
    ) {
        $cacheFile = $this->createCacheFile($config);
        if (!$fileExists) {
            unlink($cacheFile);
        }

        $this->merger->expects($mergerCalled ? $this->once() : $this->never())
            ->method('merge');

        $config = array();

        $this->assertEquals(
            $expected,
            $this->router->loadCachedConfig($config),
            $message
        );

        if ($fileExists) {
            unlink($cacheFile);
        }
    }
}
