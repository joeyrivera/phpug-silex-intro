<?php

set_time_limit(0);

$app = require_once('bootstrap.php');

use App\Command\DbReset;

$application = $app['console'];
$application->add(new DbReset());
$application->run();