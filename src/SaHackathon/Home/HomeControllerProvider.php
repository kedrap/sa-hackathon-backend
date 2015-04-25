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
                            'eventsLike' => [],
                            'eventsDislike' => [],
                        ]
                    ]
                );
            }
        );

        return $controllers;
    }
}
