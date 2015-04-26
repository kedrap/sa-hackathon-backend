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
     * Returns unique articles from entity
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
                    AND article_double.decision=\'dislike\') as articleDislikes,
                (   SELECT count(*)
                    FROM article as article_double
                    WHERE article.hash=article_double.hash
                    AND article_double.decision=\'skip\') as articleSkips
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
                ->setSkips($article['articleSkips'])
                ->setDislikes($article['articleDislikes']);

        }

        return $articleList;

    }

    /**
     * Returns article by hash from entity
     * @param $hash string
     * @return Article
     */
    public function getArticleByHash($hash)
    {

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
                    AND article_double.decision=\'dislike\') as articleDislikes,
                (   SELECT count(*)
                    FROM article as article_double
                    WHERE article.hash=article_double.hash
                    AND article_double.decision=\'skip\') as articleSkips
                '
            )
            ->from(self::ARTICLE_TABLE_NAME)
            ->where("hash='".$hash."'")
            ->groupBy('hash')
            ->execute();

        if ($articleFetched = $query->fetch()) {
            $article = new Article();
            $article
                ->setId($articleFetched['id'])
                ->setTitle($articleFetched['title'])
                ->setTime($articleFetched['time'])
                ->setHash($articleFetched['hash'])
                ->setUser($articleFetched['user'])
                ->setDecision($articleFetched['decision'])
                ->setDate($articleFetched['date'])
                ->setLikes($articleFetched['articleLikes'])
                ->setSkips($articleFetched['articleSkips'])
                ->setDislikes($articleFetched['articleDislikes']);

            return $article;

        }

        return null;

    }

    /**
     * Returns statistics from entity
     * @param $hash
     * @return null
     */
    public function getArticleStatistics($hash)
    {

        $query = $this
            ->getConnection()
            ->createQueryBuilder()
            ->select(
                '
                *,
                (
                    SELECT if (sum(time) IS NULL,0,sum(time))
                    FROM article as article_double
                    WHERE article.hash=article_double.hash
                    AND article_double.decision=\'like\'
                    AND date > CURRENT_TIMESTAMP() - INTERVAL 30 second
                ) as timeLikes,
                (
                    SELECT if (sum(time) IS NULL,0,sum(time))
                    FROM article as article_double
                    WHERE article.hash=article_double.hash
                    AND article_double.decision=\'dislike\'
                    AND date > CURRENT_TIMESTAMP() - INTERVAL 5 second
                ) as timeDislikes,
                (
                    SELECT if (sum(time) IS NULL,0,sum(time))
                    FROM article as article_double
                    WHERE article.hash=article_double.hash
                    AND article_double.decision=\'skip\'
                    AND date > CURRENT_TIMESTAMP() - INTERVAL 5 second
                ) as timeSkip
                '
            )
            ->from(self::ARTICLE_TABLE_NAME)
            ->where("hash='".$hash."'")
            ->andWhere('date > CURRENT_TIMESTAMP() - INTERVAL 5 second')
            ->groupBy('hash')
            ->execute();

        if ($articleFetched = $query->fetch()) {

            $article = new Article();
            $article
                ->setId($articleFetched['id'])
                ->setTitle($articleFetched['title'])
                ->setTime($articleFetched['time'])
                ->setHash($articleFetched['hash'])
                ->setUser($articleFetched['user'])
                ->setDecision($articleFetched['decision'])
                ->setDate($articleFetched['date'])
                ->setTimeLikes($articleFetched['timeLikes'])
                ->setTimeDislikes($articleFetched['timeDislikes'])
                ->setTimeSkip($articleFetched['timeSkip']);

            return $article;

        }

        return null;
    }

}
