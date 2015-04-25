<?php

namespace SaHackathon\Home;

use SaHackathon\Home\Api\Repository\ArticleRepository;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class HomeControllerProvider implements ControllerProviderInterface
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
        $this->initTwig(__DIR__ . '/views');

        $controllers = $app['controllers_factory'];

        $that = $this;
        $controllers->get(
            '/',
            function(Application $app) use($that) {

                $service = $app['eventSaver'];
                $articleList = $service->getUniqueArticles();

                return $that->twig->render(
                    'index.html.twig',
                    [
                        'articles' => $articleList
                    ]
                );
            }
        );

        $controllers->get(
            '/article/{slug}',
            function($slug) use($that) {
                return $this->twig->render(
                    'article.html.twig',
                    [
                        'article' => [
                            'slug' => $slug,
                            'title' => 'Rosenborg-ledelsen visste ikke at Nicki Bille var dømt tidligere',
                            'eventsLike' => [
                                [
                                    'decision' => 'like',
                                    'color' => '#009900',
                                    'y' => 15,
                                    'x' => strtotime('2015-04-24T23:00:00Z') * 1000
                                ],
                                [
                                    'decision' => 'like',
                                    'color' => '#009900',
                                    'y' => 25,
                                    'x' => strtotime('2015-04-24T23:01:00Z') * 1000
                                ],
                                [
                                    'decision' => 'like',
                                    'color' => '#009900',
                                    'y' => 5,
                                    'x' => strtotime('2015-04-24T23:03:00Z') * 1000
                                ],
                                [
                                    'decision' => 'like',
                                    'color' => '#009900',
                                    'y' => 17,
                                    'x' => strtotime('2015-04-24T23:05:00Z') * 1000
                                ],
                                [
                                    'decision' => 'like',
                                    'color' => '#009900',
                                    'y' => 45,
                                    'x' => strtotime('2015-04-24T23:06:00Z') * 1000
                                ],
                                [
                                    'decision' => 'like',
                                    'color' => '#009900',
                                    'y' => 75,
                                    'x' => strtotime('2015-04-24T23:08:00Z') * 1000
                                ],
                                [
                                    'decision' => 'like',
                                    'color' => '#009900',
                                    'y' => 30,
                                    'x' => strtotime('2015-04-24T23:09:00Z') * 1000
                                ],
                                [
                                    'decision' => 'like',
                                    'color' => '#009900',
                                    'y' => 25,
                                    'x' => strtotime('2015-04-24T23:10:00Z') * 1000
                                ]
                            ],
                            'eventsDislike' => [
                                [
                                    'decision' => 'dislike',
                                    'color' => '#990000',
                                    'y' => 35,
                                    'x' => strtotime('2015-04-24T23:02:00Z') * 1000
                                ],
                                [
                                    'decision' => 'dislike',
                                    'color' => '#990000',
                                    'y' => 7,
                                    'x' => strtotime('2015-04-24T23:04:00Z') * 1000
                                ],
                                [
                                    'decision' => 'dislike',
                                    'color' => '#990000',
                                    'y' => 5,
                                    'x' => strtotime('2015-04-24T23:07:00Z') * 1000
                                ],
                                [
                                    'decision' => 'dislike',
                                    'color' => '#990000',
                                    'y' => 65,
                                    'x' => strtotime('2015-04-24T23:09:00Z') * 1000
                                ],
                                [
                                    'decision' => 'dislike',
                                    'color' => '#990000',
                                    'y' => 75,
                                    'x' => strtotime('2015-04-24T23:11:00Z') * 1000
                                ],
                                [
                                    'decision' => 'dislike',
                                    'color' => '#990000',
                                    'y' => 5,
                                    'x' => strtotime('2015-04-24T23:13:00Z') * 1000
                                ],
                                [
                                    'decision' => 'dislike',
                                    'color' => '#990000',
                                    'y' => 20,
                                    'x' => strtotime('2015-04-24T23:15:00Z') * 1000
                                ],
                                [
                                    'decision' => 'dislike',
                                    'color' => '#990000',
                                    'y' => 37,
                                    'x' => strtotime('2015-04-24T23:17:00Z') * 1000
                                ],
                            ],
                        ]
                    ]
                );
            }
        );

        return $controllers;
    }
}
