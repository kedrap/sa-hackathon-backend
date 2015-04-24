<?php

namespace SaHackathon\Home\Api;

use SaHackathon\Home\Api\Exception\ValidationException;
use SaHackathon\Home\Api\Service\SaverInterface;
use SaHackathon\Home\TwigTrait;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class EventsControllerProvider implements ControllerProviderInterface
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
     * @return mixed
     */
    protected function initController(Application $app)
    {
        $this->initTwig(__DIR__ . '/../views');

        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $that = $this;
        $controllers->post(
            '/events',
            function(Application $app) use($that) {
                /** @var Request $request */
                $request = $app['request'];

                $data = [
                    'title' => $request->get('title'),
                    'decision' => $request->get('decision'),
                    'time' => $request->get('time'),
                    'user' => $request->get('user'),
                ];

                try {
                    /** @var SaverInterface $service */
                    $service = $app['eventSaver'];
                    $service->save($data);

                    $status = 201;
                } catch (ValidationException $e) {
                    $status = 400;
                }

                return json_encode(
                    [
                        'status' => $status,
                    ]
                );
            }
        );

        $controllers->get(
            '/events-test',
            function(Application $app) use($that) {
                return $that->twig->render('api/events-test.html.twig');
            }
        );

        return $controllers;
    }
}
