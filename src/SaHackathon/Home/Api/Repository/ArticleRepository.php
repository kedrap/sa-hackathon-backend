<?php

namespace SaHackathon\Home\Api\Repository;

use Doctrine\DBAL\Connection;
use SaHackathon\Home\Api\Entity\Article;
use SaHackathon\Home\Api\Exception\DatabaseException;

class ArticleRepository
{

    const ARTICLE_TABLE_NAME = 'article';

    private $dbConnection = null;


    /**
     * @param Connection $dbConnection
     */
    function __construct(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        return $this->dbConnection;
    }

    /**
     * Saves article
     * @param Article $article
     * @throws DatabaseException
     */
    public function save(Article $article)
    {

        $result = $this
            ->getConnection()
            ->createQueryBuilder()
            ->insert(self::ARTICLE_TABLE_NAME)
            ->values(
                [
                    'title' => '?',
                    'decision' => '?',
                    'time' => '?',
                    'user' => '?',
                    'hash' => '?',
                    'date' => '?'
                ]
            )
            ->setParameter(0, $article->getTitle())
            ->setParameter(1, $article->getDecision())
            ->setParameter(2, $article->getTime())
            ->setParameter(3, $article->getUser())
            ->setParameter(4, $article->getHash())
            ->setParameter(5, $article->getDate())
            ->execute();

        if (!$result) {
            throw new DatabaseException("Problem with saving data");
        }

    }

}
