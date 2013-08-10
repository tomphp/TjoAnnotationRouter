<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouterTests\Parser;

use TjoAnnotationRouter\Parser\ControllerAnnotationProcessor;

/**
 * Unit tests for {@see ControllerAnnotationProcessor}.
 *
 * @covers TjoAnnotationRouter\Parser\ControllerAnnotationProcessor
 *
 * @author Tom Oram <tom@scl.co.uk>
 */
class ControllerAnnotationProcessorTest extends \PHPUnit_Framework_TestCase
{
    protected $processor;

    /**
     * Set up the instance to be tested.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->processor = new ControllerAnnotationProcessor();
    }

    public function testGetControllerNamesReturnsAnArray()
    {

    }
}
