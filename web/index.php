<?php

require_once __DIR__.'/../vendor/autoload.php';

use SaHackathon\Home\HomeControllerProvider;
use SaHackathon\Home\Api\EventsControllerProvider;
use SaHackathon\Home\Api\Service\Event\EventDbSaverService;
use Symfony\Component\Yaml\Yaml;

$app = new Silex\Application();

$app->mount('', new HomeControllerProvider());
$app->mount('api', new EventsControllerProvider());

$config = Yaml::parse(file_get_contents('../config/parameters.yml'));

$app['eventSaver.dbParams'] = $config['parameters']['database'];

$app['eventSaver'] = function ($app) {
    return new EventDbSaverService($app['eventSaver.dbParams']);
};

$app->run();
