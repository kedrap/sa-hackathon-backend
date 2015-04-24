<?php

namespace SaHackathon\Home\Api\Service;

use SaHackathon\Home\Api\Exception\ValidationException;

interface SaverInterface
{
    /**
     * Saving an event.
     *
     * @param array $data
     * @throws ValidationException
     */
    public function save(array $data);
}
