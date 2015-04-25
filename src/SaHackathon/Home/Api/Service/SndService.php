<?php

namespace SaHackathon\Home\Api\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;
use SaHackathon\Home\Api\Exception\SndException;

class SndService
{
    const SND_RELATION_TYPE_AUTO = 'http://www.snd.no/types/relation/auto';

    /**
     * @var string
     */
    protected $sndPublicationId = 'sa';

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $apiSecret;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @param string $apiKey
     * @param string $apiSecret
     * @param Client $client
     */
    public function __construct($apiKey, $apiSecret, Client $client)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->client = $client;
    }

    /**
     * @param string $sectionName
     * @param array $excludeHashes
     * @param int $offset
     * @param int $limit
     *
     * @return array
     * @throws SndException
     */
    public function getArticles($sectionName, $excludeHashes = [], $offset = 0, $limit = 20)
    {
        $articleUrls = $this->getSectionArticleUrls($sectionName, $offset, $limit);

        if (false === $articleUrls) {
            throw new SndException('Empty article urls list');
        }

        $articlesRawData = $this->apiGet($articleUrls);
        if (false === $articlesRawData) {
            throw new SndException('Can not get articles raw data');
        }

        if (!isset($articlesRawData['entries'])) {
            throw new SndException('Empty entries list');
        }

        $result = [];
        foreach ($articlesRawData['entries'] as $entry) {
            if (!in_array($this->getArticleHash($entry), $excludeHashes)) {
                $result [] = $this->extractArticleData($entry);
            }
        }

        return $result;
    }

    /**
     * @param string $sectionName
     * @param int $offset
     * @param int $limit
     *
     * @return string
     */
    private function getSectionArticleUrls($sectionName, $offset = 0, $limit = 20)
    {
        $response = $this->apiGet("/news/v1/publication/{publication}/sections/instance?uniqueName=" . $sectionName);

        $link = '';
        foreach ($response['links'] as $linkArray) {
            if ($linkArray['rel'] == self::SND_RELATION_TYPE_AUTO) {
                $link = $linkArray['self'];
            }
        }

        return str_replace(['{offset?}', '{limit?}'], [$offset, $limit], $link);
    }

    /**
     * @param string $url
     *
     * @return array
     * @throws TransferException
     */
    public function apiGet($url)
    {
        $url = str_replace('{publication}', $this->sndPublicationId, $url);

        /** @var Request $request */
        $request = $this->client->createRequest('GET', $url);
        $request->addHeader('Accept', 'application/json');
        $request->addHeader('Accept-Charset', 'UTF-8');
        $request->addHeader('x-snd-apisignature', $this->generateApiSignature());

        /** @var Response $response */
        $response = $this->client->send($request);

        return $response->json();
    }

    /**
     * @return string
     */
    protected function generateApiSignature()
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $signature = hash_hmac('sha256', $now->format('d M Y H'), $this->apiSecret);
        $signature = '0x' . $signature;

        return $signature;
    }

    /**
     * @param array $article
     *
     * @return string|null
     */
    protected function getImage($article)
    {
        if (isset($article['links'])) {
            foreach ($article['links'] as $link) {
                if ($link['rel'] == 'TEASERREL') {
                    return str_replace(
                        ['{snd:mode}', '{snd:cropversion}'],
                        ['ALTERNATES', 'w480c43'],
                        $link['href']
                    );
                }
            }
        }

        return null;
    }

    /**
     * @param array $article
     * @param string $name
     *
     * @return string
     */
    protected function getExtensionChildren($article, $name)
    {
        if (!isset($article['extensions']) || !is_array($article['extensions'])) {
            return '';
        }

        foreach ($article['extensions'] as $extension) {
            if ($extension['name'] !== $name) {
                continue;
            }

            if (isset($extension['children']) && isset($extension['children'][0])) {
                return array_pop($extension['children']);
            }
        }

        return '';
    }

    /**
     * @param array $apiItem
     *
     * @return array
     */
    protected function extractArticleData($apiItem)
    {
        list($leadText, $bodyText, $byline) = $this->getArticleContent($apiItem['id']);

        $result = [
            'hash' => $this->getArticleHash($apiItem),
            'title' => $apiItem['title'],
            'description' => $leadText ? $leadText : $apiItem['summary'],
            'created' => $apiItem['published'],
            'link' => $this->getExtensionChildren($apiItem, 'snd:origUrl'),
            'image' => $this->getImage($apiItem),
            'sndId' => $apiItem['id'],
            'detail' => $bodyText,
            'author' => $byline,
        ];

        return $result;
    }

    /**
     * @param array $article
     *
     * @return string
     */
    protected function getArticleHash($article)
    {
        return md5($article['id']);
    }

    /**
     * @param string $articleUrl
     *
     * @return array
     */
    protected function getArticleContent($articleUrl)
    {
        $response = $this->apiGet($articleUrl);

        if (!isset($response['content'])) {
            return [];
        }

        $content = $response['content'];

        $leadText = isset($content['leadtext']) ? $content['leadtext'] : '';

        $byline = '';
        if (
            isset($content['bylineplacement'])
            && filter_var($content['bylineplacement'], FILTER_VALIDATE_BOOLEAN)
            && isset($content['byline'])
        ) {
            $byline = $content['byline'];
        }

        $html = preg_replace("/<img[^>]+\>/i", " ", $content['bodytext']);

        return [$leadText, $html, $byline];
    }
}
