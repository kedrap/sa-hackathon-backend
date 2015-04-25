<?php

namespace SaHackathon\Home\Api\Service\Event;

use Doctrine\DBAL\DriverManager;
use SaHackathon\Home\Api\Entity\Article;
use SaHackathon\Home\Api\Repository\ArticleRepository;
use SaHackathon\Home\Api\Exception\ValidationException;

class EventDbSaverService extends EventSaverService
{
    private $articleRepository = null;

    /**
     * @param array $dbParams
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct(array $dbParams)
    {

        $this->articleRepository = new ArticleRepository(
            DriverManager::getConnection($dbParams)
        );

    }

    /**
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        if (!$this->areValid($data)) {
            throw new ValidationException('Data are not valid');
        }

        $article = new Article();

        $article
            ->setTime($data['time'])
            ->setDecision($data['decision'])
            ->setTitle($data['title'])
            ->setHash($data['hash'])
            ->setUser($data['user']);

        $this->articleRepository->save($article);

    }

    /**
     * @return \SaHackathon\Home\Api\Entity\Article[]
     */
    public function getUniqueArticles ()
    {

        return $this->articleRepository->getUniqueArticles();

    }

}
