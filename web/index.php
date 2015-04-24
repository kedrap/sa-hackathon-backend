<?php

require_once __DIR__.'/../vendor/autoload.php';

use SaHackathon\Home\HomeControllerProvider;

$app = new Silex\Application();

$app->mount('', new HomeControllerProvider());

$app->run();
