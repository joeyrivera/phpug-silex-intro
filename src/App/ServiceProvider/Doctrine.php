<?php
/**
 * Created by PhpStorm.
 * User: joey.rivera
 * Date: 9/29/15
 * Time: 9:43 PM
 */

namespace App\ServiceProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Doctrine implements ServiceProviderInterface
{
	public function register(Application $app)
	{
		$paths = array(realpath($app['config']['doctrine']['paths']['entity']));
		$isDevMode = $app['config']['doctrine']['devMode'];
		$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);

		$app['doctrine'] = EntityManager::create($app['config']['doctrine']['db'], $config);
	}

	public function boot(Application $app)
	{

	}
}
