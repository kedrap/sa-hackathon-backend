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
                /** @var Request $request */
                $request = $app['request'];

                $limit = $request->get('limit', 20);
                $excludeHashes = json_decode($request->get('excludeHashes', '[]'), true);
                $excludeHashes = array_keys($excludeHashes);

                $json = file_get_contents($app['articlesBasePath'] . '/articles.json');
                $response =  $json ?
                    $this->getArticlesFromString($json, $excludeHashes, $limit) :
                    $this->getArticlesFromSndApi($app, $excludeHashes, $limit);

                return json_encode($response);
            }
        );

        return $controllers;
    }

    /**
     * @param string $json
     * @param array $excludeHashes
     * @param int $limit
     *
     * @return array
     */
    protected function getArticlesFromString($json, $excludeHashes, $limit)
    {
        $articles = json_decode($json, true);
        if (!$articles) {
            return [];
        }

        $response = [];
        while (count($response) < $limit) {
            $article = array_shift($articles);
            if ($article == null) {
                break;
            }

            if (!in_array($article['hash'], $excludeHashes)) {
                $response [] = $article;
            }
        }

        return $response;
    }

    /**
     * @param Application $app
     * @param array $excludeHashes
     * @param int $limit
     *
     * @return array
     */
    protected function getArticlesFromSndApi($app, $excludeHashes, $limit)
    {
        /** @var SndService $sndService */
        $sndService = $app['sndClient'];

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

        return $response;
    }
}

