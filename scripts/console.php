<?php

use Knp\Console\Application;
use Knp\Provider\ConsoleServiceProvider;
use SaHackathon\Home\Command\ArticlesImporterCommand;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app->register(new ConsoleServiceProvider(), [
    'console.name' => 'ConsoleApp',
    'console.version' => '1.0.0',
    'console.project_directory' => __DIR__ . '/..'
]);

/** @var Application $appConsole */
$appConsole = $app['console'];
$appConsole->add(new ArticlesImporterCommand(__DIR__ . '/../'));
$appConsole->run();
