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

* `@Router\Base` - Sets the route that all annotated routes fall under. You can use / to specify multiple levels.
* `@Router\Route` - Sets the route for this action.
* `@Router\DefaultValue` - Set a default value for a parameter, use one of these annotations for each param you wish to specify a value for.
* `@Router\Constraint` - Set a constraint for a parameter, again use mulitple annotations for multiple parameters.

Configuring the module
----------------------

Currently the only configuration option available is the path to the cache file.
If you wish to update this simply add this to your project/module config:

```php
    'tjo_annotation_router' => array(
        'cache_file' => 'path/to/cache/file.php',
    ),
```

Caching
=======

Parsing the route annotations every request will slow down your application quite significantly. To combat this a
caching solution is provided. To build the cache simple run the following command from the command line:

`vendor/bin/cache_routes.php`

If you wish, you modify any annotations after building the cache simply run this command again.

If you want to turn off to caching at any time just remove `data/TjoAnnotation/routes.php`.


TODO List
=========

This is currently a very early version of the module. Jobs on my TODO list are:

* Build a test suite
* Refactor
* Bug fix
* Improve functionality

Please comment, suggest, fork, etc.

