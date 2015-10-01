<?php

// set pwd to application root
chdir(__DIR__);

// load composer
require_once "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$config = require_once('config' . DIRECTORY_SEPARATOR . 'global.php');

// doctrine setup
$paths = array(realpath($config['doctrine']['paths']['entity']));
$isDevMode = $config['doctrine']['devMode'];

$entityManager = EntityManager::create($config['doctrine']['db'], Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false));

return ConsoleRunner::createHelperSet($entityManager);
