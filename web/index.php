<?php

require_once __DIR__.'/../vendor/autoload.php';

use SaHackathon\Home\HomeControllerProvider;
use SaHackathon\Home\Api\EventsControllerProvider;
use SaHackathon\Home\Api\Service\Event\EventFileSaverService;

$app = new Silex\Application();

$app->mount('', new HomeControllerProvider());
$app->mount('api', new EventsControllerProvider());

$app['eventSaver.path'] = __DIR__ . '/../events';
$app['eventSaver'] = function ($app) {
    $service = new EventFileSaverService();
    $service->setPath($app['eventSaver.path']);

    return $service;
};

$app->run();
