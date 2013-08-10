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
     *
     * @var \Zend\Mvc\Controller\ControllerManager
     */
    protected $controllerManager;

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

        $this->controllerManager = $this->getMock('Zend\Mvc\Controller\ControllerManager');

        $this->router = new AnnotationRouter(
            $this->config,
            $this->parser,
            $this->merger,
            $this->controllerManager
        );
    }

    /*
     * controllerList()
     */

    public function testControllerListReturnsAnArray()
    {
        $this->setCanonicalNames(array());

        $this->assertInternalType('array', $this->router->controllerList());
    }

    public function testControllerListResultIsKeyedByCanonicalNames()
    {
        $this->setCanonicalNames(array('test-controller', 'another-controller'));

        $this->assertArrayHasKey('test-controller', $this->router->controllerList());
        $this->assertArrayHasKey('another-controller', $this->router->controllerList());
    }

    public function testControllerListReturnsTheControllers()
    {
        $controllerName = 'test-controller';
        $controller     = 'the-controller-class';

        $this->setCanonicalNames(array($controllerName));

        $this->controllerManager
             ->expects($this->once())
             ->method('get')
             ->with($this->equalTo($controllerName))
             ->will($this->returnValue($controller));

        $list = $this->router->controllerList();

        $this->assertSame($controller, $list[$controllerName]);
    }

    protected function setCanonicalNames(array $names)
    {
        $this->controllerManager
             ->expects($this->any())
             ->method('getCanonicalNames')
             ->will($this->returnValue($names));
    }

    /*
     * loadCachedConfig
     */

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
     * @covers TjoAnnotationRouter\AnnotationRouter::loadCachedConfig
     * @dataProvider cacheConfigProvider
     *
     * @param boolean $expected
     * @param string  $message
     * @param boolean $fileExists
     * @param boolean $mergerCalled
     * @param array   $config
     */
    public function testLoadCachedConfig(
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

    /*
     * getRouteConfig()
     */

    public function testGetRouteConfigReturnsArray()
    {
        $this->assertInternalType('array', $this->router->getRouteConfig(array()));
    }

    public function testGetRouteConfigCheckThatControllerAreReflectedAndProcessed()
    {
        $controllerName  = 'the-name';
        $controller      = 'the-controller';

        $controllers = array($controllerName => $controller);

        $this->setupControllerParsingChecks(array(), $controllers);

        $this->router->getRouteConfig($controllers);
    }

    public function testGetRouteConfigCheckTheResultIsTheUpdatedConfig()
    {
        $config = array('the-test-config');

        $controllers = array(
            'cn1' => 'ci1',
            'cn2' => 'ci2',
        );

        $this->setupControllerParsingChecks($config, $controllers);

        $this->assertEquals($config, $this->router->getRouteConfig($controllers));
    }

    /**
     * Set up the mocks and checks for parsing the controller annotations.
     *
     * @param  array $config
     * @param  array $controllers
     * @return void
     */
    protected function setupControllerParsingChecks(array $config, array $controllers)
    {
        $configContainer = new \ArrayObject($config);

        $reflections = array();

        $count = 0;

        $first = true;

        foreach ($controllers as $name => $controller) {
            $reflection = $this->setupGetReflectedControllerCheck($count, $controller);

            $count++;

            $this->setupParseReflectedControllerCheck($count, $name, $reflection, $configContainer, $first);

            $count++;

            $first = false;
        }
    }

    /**
     * Set up a check for calls the getReflectedController.
     *
     * @param  int   $at
     * @param  mixed $controller
     * @return \Zend\Code\Reflection\ClassReflection
     */
    protected function setupGetReflectedControllerCheck($at, $controller)
    {
        $reflection = $this->getMockBuilder('Zend\Code\Reflection\ClassReflection')
                           ->disableOriginalConstructor()
                           ->getMock();

        $this->parser
             ->expects($this->at($at))
             ->method('getReflectedController')
             ->with($this->identicalTo($controller))
             ->will($this->returnValue($reflection));

        return $reflection;
    }

    /**
     * Set up a check for calls to parseReflectedController.
     *
     * @param  int             $at
     * @param  string          $controllerName
     * @param  ClassReflection $reflection
     * @param  \ArrayObject    $configContainer
     * @param  bool            $first           Is this the first iteration of the loop.
     * @return void
     */
    protected function setupParseReflectedControllerCheck($at, $controllerName, $reflection, $configContainer, $first)
    {
        $this->parser
             ->expects($this->at($at))
             ->method('parseReflectedController')
             ->with(
                 $this->equalTo($controllerName),
                 $this->identicalTo($reflection),
                 ($first ? $this->isInstanceOf('ArrayObject') : $this->identicalTo($configContainer))
             )
             ->will($this->returnValue($configContainer));
    }
}
