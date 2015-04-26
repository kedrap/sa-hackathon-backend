<?php

namespace SaHackathon\Home\Command;

use GuzzleHttp\Client;
use Knp\Command\Command;
use SaHackathon\Home\Api\Service\SndService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ArticlesImporterCommand extends Command
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @param string $path
     * @param null $name
     */
    public function __construct($path, $name = null)
    {
        parent::__construct($name);

        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('import')
            ->setDescription('Import articles from SND API')
        ; // nice, new line
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sndService = new SndService(
            '2ihe4RSAF6oapHSrZTdPSLeSv',
            'W7f5GbvfVc5dXBbx4hCK8tYU6',
            new Client(['base_url' => 'http://api.snd.no'])
        );

        $articles = $sndService->getArticles('sport', [], 0, 100);

        file_put_contents($this->path . '/articles.json', json_encode($articles));

        echo 'OK';
    }
}
