<?php
/**
 * TjoAnnotationRouter library (https://github.com/tomphp/TjoAnnotationRouter)
 *
 * @link https://github.com/tomphp/TjoAnnotationRouter for the canonical source repository
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace TjoAnnotationRouter\Config;

/**
 * Tests for {@see Merger}.
 *
 * @author Tom Oram <tom@x2k.co.uk>
 */
class MergerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The instance we are going to test.
     *
     * @var Merger
     */
    protected $merger;

    /**
     * Prepare an instance to test.
     */
    protected function setUp()
    {
        $this->merger = new Merger();
    }

    /**
     * @covers TjoAnnotationRouter\Config\Merger::merge
     */
    public function testMerge2SeperateRoutes()
    {
        $config = array(
            'route1' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/route-one',
                ),
            ),
        );

        $newConfig = array(
            'route2' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/route-two',
                ),
            ),
        );

        $expected = array(
            'route1' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/route-one',
                ),
            ),
            'route2' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/route-two',
                ),
            ),
        );

        $this->merger->merge($newConfig, $config);

        $this->assertEquals($expected, $config);
    }

    /**
     * @covers TjoAnnotationRouter\Config\Merger::merge
     */
    public function testMergeDoesntOverwriteExisting()
    {
        $config = array(
            'route1' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/route-one',
                ),
            ),
        );

        $newConfig = array(
            'route1' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/route-xxx',
                ),
            ),
        );

        $expected = $config;

        $this->merger->merge($newConfig, $config);

        $this->assertEquals($expected, $config);
    }

    /**
     * @covers TjoAnnotationRouter\Config\Merger::merge
     */
    public function testMergeCreatesBranch()
    {
        $config = array();

        $newConfig = array(
            'new-branch' => array(
                'child_routes' => array(
                    'new-route' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/route-xxx',
                        ),
                    ),
                ),
            )
        );

        $expected = array(
            'new-branch' => array(
                'type' => 'Literal',
                'may_terminate' => false,
                'options' => array(
                    'route' => '/new-branch',
                ),
                'child_routes' => array(
                    'new-route' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/route-xxx',
                        ),
                    ),
                ),
            )
        );

        $this->merger->merge($newConfig, $config);

        $this->assertEquals($expected, $config);
    }

    /**
     * @covers TjoAnnotationRouter\Config\Merger::merge
     */
    public function testMergeModifiesBranch()
    {
        $config = array(
            'branch' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/the-branch',
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'existing-route' => array(
                    ),
                ),
            )
        );

        $newConfig = array(
            'branch' => array(
                'child_routes' => array(
                    'new-route' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/route-xxx',
                        ),
                    ),
                ),
            )
        );

        $expected = array(
            'branch' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/the-branch',
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'existing-route' => array(
                    ),
                    'new-route' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/route-xxx',
                        ),
                    ),
                ),
            )
        );

        $this->merger->merge($newConfig, $config);

        $this->assertEquals($expected, $config);
    }
}
