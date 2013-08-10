<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouterTests\Parser;

use TjoAnnotationRouter\Parser\ControllerParser;

/**
 * Unit tests for {@see ControllerParser}.
 *
 * @covers TjoAnnotationRouter\Parser\ControllerParser
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ControllerParserTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;

    protected $annotationManager;

    protected $processor;

    /**
     * Set up the instance to be tested.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->annotationManager = $this->getMock('Zend\Code\Annotation\AnnotationManager');

        $this->processor = $this->getMock('TjoAnnotationRouter\Parser\ControllerAnnotationProcessor');

        $this->parser = new ControllerParser(
            $this->annotationManager,
            $this->processor
        );
    }

    /*
     * getReflectedController()
     */

    public function testGetReflectedControllerReturnsClassReflection()
    {
        $this->assertInstanceOf(
            'Zend\Code\Reflection\ClassReflection',
            $this->parser->getReflectedController($this)
        );
    }

    public function testGetReflectedControllerReturnsReflectionOfGivenClass()
    {
        $this->assertEquals(
            get_class($this),
            $this->parser->getReflectedController($this)->getName()
        );
    }
}
