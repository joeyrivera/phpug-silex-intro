<?php

// set pwd to application root
chdir(__DIR__);

require_once "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

$app = new Silex\Application();
$app['config'] = require_once('config' . DIRECTORY_SEPARATOR . 'global.php');
$app['debug'] = true;

/**
 * register providers
 */
$app->register(new App\ServiceProvider\Doctrine());
$app->register(new Knp\Provider\ConsoleServiceProvider(), array(
	'console.name'              => 'CLI Tools',
	'console.version'           => '1.0.0',
	'console.project_directory' => getcwd()
));

/**
 * handle errors
 */
$app->error(function (\Exception $e) use ($app) {
	return $app->json([
		'error' => $e->getMessage(),
		'code' => $e->getCode()
	], $e->getCode());
});

/**
 * handle authentication
 */
// authenticate
//$app->before(function(\Symfony\Component\HttpFoundation\Request $request) use ($app) {
//	$token = $request->query->getAlnum('token', null);
//
//	if (empty($token)) {
//		throw new \Exception("Not authenticated", 401);
//	}
//
//	$user = $app['doctrine']->getRepository('App\Entity\User')->findOneBy([
//		'token' => $token
//	]);
//
//	if (empty($user)) {
//		throw new \Exception("Not valid token", 401);
//	}
//
//	$app['user'] = $user;
//});

/**
 * handle endpoints
 */
$app->get('/users', function (Silex\Application $app) {
	$users = $app['doctrine']->getRepository('App\Entity\User')->findAll();

	if (empty($users)) {
		throw new \InvalidArgumentException("No users found", 404);
	}

	$data = [];
	foreach ($users as $user) {
		$data[] = [
			'id' => $user->getId(),
			'firstName' => $user->getFirstName(),
			'lastName' => $user->getLastName()
		];
	}

	return $app->json($data);
});

$app->get('/users/{id}', function (Silex\Application $app, $id) {
	$user = $app['doctrine']->getRepository('App\Entity\User')->find($id);

	if (empty($user)) {
		throw new \InvalidArgumentException("User not found", 404);
	}

	return $app->json([
		'id' => $user->getId(),
		'firstName' => $user->getFirstName(),
		'lastName' => $user->getLastName()
	]);
});

$app->post('/users', function (Silex\Application $app, \Symfony\Component\HttpFoundation\Request $request) {
	$user = new \App\Entity\User();
	$user->setFirstName($request->get('firstName'));
	$user->setLastName($request->get('lastName'));

	$app['doctrine']->persist($user);
	$app['doctrine']->flush();

	return $app->json([
		'id' => $user->getId(),
		'firstName' => $user->getFirstName(),
		'lastName' => $user->getLastName()
	]);
});

$app->put('/users/{id}', function (Silex\Application $app, \Symfony\Component\HttpFoundation\Request $request, $id) {
	$user = $app['doctrine']->getRepository('App\Entity\User')->find($id);
	$user->setFirstName($request->get('firstName'));
	$user->setLastName($request->get('lastName'));

	$app['doctrine']->merge($user);
	$app['doctrine']->flush();

	return $app->json([
		'id' => $user->getId(),
		'firstName' => $user->getFirstName(),
		'lastName' => $user->getLastName()
	]);
});

$app->delete('/users/{id}', function (Silex\Application $app, $id) {
	$user = $app['doctrine']->getRepository('App\Entity\User')->find($id);

	$app['doctrine']->remove($user);
	$app['doctrine']->flush();

	return $app->json([]);
});


return $app;