<?php

namespace SaHackathon\Home\Api\Service\Event;

use SaHackathon\Home\Api\Exception\ValidationException;

class EventFileSaverService extends EventSaverService
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
    /**
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        if (!$this->areValid($data)) {
            throw new ValidationException('Data are not valid');
        }

        if (empty($this->path)) {
            throw new ValidationException('Missing "path" parameter');
        }

        file_put_contents(sprintf('%s/ %s-%s', $this->path, $data['user'], time()), json_encode($data));
    }
}
