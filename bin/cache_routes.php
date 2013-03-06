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

/* @var $config \TjoAnnotationRouter\Options\Config */
$config = $serviceLocator->get('TjoAnnotationRouter\Options\Config');

/* @var $annotationRouter \TjoAnnotationRouter\AnnotationRouter */
$annotationRouter = $serviceLocator->get('TjoAnnotationRouter\AnnotationRouter');

$routeConfig = $annotationRouter->getRouteConfig();

$cacheFile = $config->getCacheFile();

$cacheDir = dirname($cacheFile);

if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

$cacheData = var_export($routeConfig, true);

$fp = fopen(getcwd() . '/' . $cacheFile, 'w');

if (!$fp) {
    die('Failed to open ' . $cacheFile ." for writing\n" );
}

fputs($fp, "<?php\n return $cacheData;\n");

fclose($fp);
