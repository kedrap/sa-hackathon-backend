<?php

require_once __DIR__.'/../vendor/autoload.php';

use SaHackathon\Home\HomeControllerProvider;
use SaHackathon\Home\Api\EventsControllerProvider;
use SaHackathon\Home\Api\Service\Event\EventDbSaverService;
use Symfony\Component\Yaml\Yaml;
use SaHackathon\Home\Data\DataControllerProvider;
use GuzzleHttp\Client;
use SaHackathon\Home\Api\Service\SndService;
use SaHackathon\Home\Api\SndControllerProvider;

$app = new Silex\Application();

$app['debug'] = true;

$app->mount('', new HomeControllerProvider());
$app->mount('api', new EventsControllerProvider());
$app->mount('data', new DataControllerProvider());
$app->mount('api/snd', new SndControllerProvider());

$config = Yaml::parse(file_get_contents('../config/parameters.yml'));

$app['eventSaver.dbParams'] = $config['parameters']['database'];

$app['eventSaver'] = function ($app) {
    return new EventDbSaverService($app['eventSaver.dbParams']);
};

$app['guzzleClient'] = function () {
    return new Client(['base_url' => 'http://api.snd.no']);
};

$app['sndClientId'] = '2ihe4RSAF6oapHSrZTdPSLeSv';
$app['sndClientSecret'] = 'W7f5GbvfVc5dXBbx4hCK8tYU6';

$app['sndClient'] = function ($app) {
    $clientId = $app['sndClientId'];
    $clientSecret = $app['sndClientSecret'];

    $client = $app['guzzleClient'];

    $service = new SndService($clientId, $clientSecret, $client);
    return $service;
};

$app['articlesBasePath'] = __DIR__ . '/../';

$app->run();
