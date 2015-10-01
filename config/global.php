<?php

return [
	'doctrine' => [
		'paths' => [
			'entity' => 'src/App/Entity',
			'proxy' => 'src/App/Entity/Proxy'
		],
		'db' => [
			'driver' => 'pdo_mysql',
			'user' => 'root',
			'password' => '',
			'dbname' => 'app',
			'port' => 3306
		],
		'devMode' => false
	]
];