<?php

namespace SaHackathon\Home\Data;

use Silex\ControllerProviderInterface;
use SaHackathon\Home\TwigTrait;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataControllerProvider implements ControllerProviderInterface
{
    use TwigTrait;

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        return $this->initController($app);
    }

    /**
     * @param Application $app
     *
     * @return mixed
     */
    protected function initController(Application $app)
    {
        $this->initTwig(__DIR__ . '/../views');

        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $that = $this;

        $controllers->get(
            '/refresh',
            function (Application $app) use ($that) {

                $date = new \DateTime();

                $event = [
                    'y' => rand(1, 5),
                    'x' => strtotime($date->format('Y-m-d H:i:s')) * 1000
                ];

                $seed = rand(1, 20);

                if ($seed % 3 === 0) {
                    $event['decision'] = 'like';
                    $event['color'] = 'green';
                    $result['eventLike'][] = $event;
                }

                if ($seed % 3 === 1) {
                    $event['decision'] = 'dislike';
                    $event['color'] = 'red';
                    $result['eventDislike'][] = $event;
                }

                if ($seed % 3 === 2) {
                    $event['decision'] = 'skip';
                    $event['color'] = 'gray';
                    $result['eventDislike'][] = $event;
                }

                return new JsonResponse($event);
            }
        );

        return $controllers;
    }
}
