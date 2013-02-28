<?php
if (!($loader = @include_once __DIR__ . '/../vendor/autoload.php')) {
    throw new RuntimeException('vendor/autoload.php could not be found. Did you run `php composer.phar install`?');
}

/* var $loader \Composer\Autoload\ClassLoader */
$loader->add('TjoAnnotationRouter\\', __DIR__ . '/src');
