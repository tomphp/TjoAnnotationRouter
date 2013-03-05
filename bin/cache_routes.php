#!/usr/bin/env php
<?php

use Zend\Mvc\Application;

ini_set('display_errors', true);
chdir(__DIR__);

$previousDir = '.';

while (!file_exists('config/application.config.php')) {
    $dir = dirname(getcwd());

    if ($previousDir === $dir) {
        throw new RuntimeException(
            'Unable to locate "config/application.config.php": ' . 
            'is TjoAnnotationRouter in a subdir of your application skeleton?'
        );
    }

    $previousDir = $dir;
    chdir($dir);
}

if (!(@include_once 'init_autoloader.php')) {
    throw new RuntimeException('Error: init_autoloader.php could not be found. Did you run php composer.phar install?');
}

$application = Application::init(include 'config/application.config.php');

$serviceLocator = $application->getServiceManager();

$config = $serviceLocator->get('Config');
$config = $config['tjo_annotation_router'];

/* @var $annotationRouter \TjoAnnotationRouter\AnnotationRouter */
$annotationRouter = $serviceLocator->get('TjoAnnotationRouter\AnnotationRouter');

$routeConfig = $annotationRouter->getRouteConfig($config['controllers']);

$cacheDir = dirname($config['cache_file']);

if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

$cacheData = var_export($routeConfig, true);

$fp = fopen(getcwd() . '/' . $config['cache_file'], 'w');

if (!$fp) {
    die('Failed to open ' . $config['cache_file'] ." for writing\n" );
}

fputs($fp, "<?php\n return $cacheData;\n");

fclose($fp);
