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

    /**
     * Returns unique articles from datasource
     * @return Article[]
     */
    public function getUniqueArticles()
    {

        $articleList = [];

        $query = $this
            ->getConnection()
            ->createQueryBuilder()
            ->select(
                '
                *,
                (   SELECT count(*)
                    FROM article as article_double
                    WHERE article.hash=article_double.hash
                    AND article_double.decision=\'like\') as articleLikes,
                (   SELECT count(*)
                    FROM article as article_double
                    WHERE article.hash=article_double.hash
                    AND article_double.decision=\'dislike\') as articleDislikes
                '
            )
            ->from(self::ARTICLE_TABLE_NAME)
            ->groupBy('hash')
            ->execute();


        $articles = $query->fetchAll();

        foreach ($articles as $id=>$article) {
            $articleList[$id] = new Article();
            $articleList[$id]
                ->setId($article['id'])
                ->setTitle($article['title'])
                ->setTime($article['time'])
                ->setHash($article['hash'])
                ->setUser($article['user'])
                ->setDecision($article['decision'])
                ->setDate($article['date'])
                ->setLikes($article['articleLikes'])
                ->setDislikes($article['articleDislikes']);

        }

        return $articleList;

    }

}
