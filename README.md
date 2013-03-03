TjoAnnotationRouter
===================

[![Build Status](https://travis-ci.org/tomphp/TjoAnnotationRouter.png?branch=master)](https://travis-ci.org/tomphp/TjoAnnotationRouter)

This module allows the use of annotations in your controller classes to
configure the router.

Installation
============

Added the following requirement to your projects composer.json file.

    "tomphp/tjo-annotation-router": "dev-master"

and run

    php ./composer.phar update

and finally add `TjoAnnotationRouter` to your modules list in
`config/application.php`.

Usage
=====

Annotating the controller
-------------------------

First up annotate your controller like so:
```php
<?php

namespace DemoModule\Controller;

use TjoAnnotationRouter\Annotation as Router;

/**
 * @Router\Controller("DemoModule\Controller\TestController")
 * @Router\Base("demo")
 */
class TestController extends AbstractActionController
{
    /**
     * @Router\Route(type="literal", name="index", route="/index")
     */
    public function indexAction()
    {
        // Action stuff here
    }

    /**
     * @Router\Route(type="segment", name="another-page", route="/page1/:id")
     * @Router\Constraint(param="id", rule="[0-9]+")
     * @Router\DefaultValue(param="id", value="7")
     */
    public function anotherPageAction()
    {
        // More action stuff here
    }
}
```

The Annotations
---------------

* `@Router\Controller` - This annotation is needed to tell the router the name of the controller. **Note: This must be the name you have used in the `controllers` section of your config, NOT the class name**
* `@Router\Base` - Sets the route that all annotated routes fall under. You can use / to specify multiple levels.
* `@Router\Route` - Sets the route for this action.
* `@Router\DefaultValue` - Set a default value for a parameter, use one of these annotations for each param you wish to specify a value for.
* `@Router\Constraint` - Set a constraint for a parameter, again use mulitple annotations for multiple parameters.

Configuring the module
----------------------

The only configuration required is to tell the module which controllers are to
be parsed by the AnnotationRouter. To do this simply add the following to your
project/module config:

```php
    'tjo_annotation_router' => array(
        'controllers' => array(
            'DemoModule\Controller\TestController',
            // Add more here if necessary
        ),
    ),
```

**Note: Here you must use the class name and NOT the controller name specified in your ZF2 config**

TODO List
=========

This is currently a very early version of the module. Jobs on my TODO list are:

* Build a test suite
* Refactor
* Bug fix
* Improve functionality
* Caching to avoid annotation passing in production

Please comment, suggest, fork, etc.

