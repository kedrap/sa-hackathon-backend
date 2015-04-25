<?php

namespace SaHackathon\Home\Api;

use SaHackathon\Home\Api\Exception\SndException;
use SaHackathon\Home\Api\Service\SndService;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class SndControllerProvider implements ControllerProviderInterface
{
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
        /** @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $that = $this;
        $controllers->get(
            '/articles',
            function(Application $app) use($that) {
                /** @var SndService $sndService */
                $sndService = $app['sndClient'];

                /** @var Request $request */
                $request = $app['request'];

                $limit = $request->get('limit', 20);
                $excludeHashes = json_decode($request->get('excludeHashes', '[]'), true);

                $response = [];
                $offset = 0;
                while (count($response) <= $limit) {
                    try {
                        $articles = $sndService->getArticles('sport', $excludeHashes, $offset, $limit);

                        $response = array_merge($response, $articles);
                        $response = array_splice($response, 0, $limit);

                        if (count($response) >= $limit) {
                            break;
                        }

                        $offset += $limit;
                    } catch (SndException $e) {
                        break;
                    }
                }

                return json_encode($response);
            }
        );

        return $controllers;
    }
}

