<?php

namespace SaHackathon\Home\Data;

use SaHackathon\Home\Api\Entity\Article;
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

                $articleHash = $app['request']->get('hash');

                $service = $app['eventSaver'];
                $articleStatistics = $service->getArticleStatistics($articleHash);

                /** @var Article $article */
                $article = $service->getArticleByHash($articleHash);

                $date = new \DateTime();

                $seed = rand(1, 20);

                if ($seed % 3 === 0) {
                    $event['decision'] = 'like';
                    $event['color'] = '#7accc8';
                    $event['y'] = ($articleStatistics) ? (int)$articleStatistics->getTimeLikes() : 0;
                    $event['x'] = strtotime($date->format('Y-m-d H:i:s')) * 1000;
                }

                if ($seed % 3 === 1) {
                    $event['decision'] = 'dislike';
                    $event['color'] = '#f26d7d';
                    $event['y'] = ($articleStatistics) ? (int)$articleStatistics->getTimeDislikes() : 0;
                    $event['x'] = strtotime($date->format('Y-m-d H:i:s')) * 1000;
                }

                if ($seed % 3 === 2) {
                    $event['decision'] = 'skip';
                    $event['color'] = '#898989';
                    $event['y'] = ($articleStatistics) ? (int)$articleStatistics->getTimeSkip() : 0;
                    $event['x'] = strtotime($date->format('Y-m-d H:i:s')) * 1000;
                }

                $eventsCount = $article->getLikes() + $article->getDislikes() + $article->getSkips(); // TODO: add also skip count
                $response = [
                    'event' => $event,
                    'article' => [
                        'likesPercent' => (int) ($article->getLikes() / $eventsCount * 100),
                        'dislikesPercent' => (int) ($article->getDislikes() / $eventsCount * 100),
                        'skipsPercent' => (int) ($article->getSkips() / $eventsCount * 100),
                    ]
                ];

                return new JsonResponse($response);
            }
        );

        return $controllers;
    }
}
