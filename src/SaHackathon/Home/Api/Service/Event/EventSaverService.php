<?php

namespace SaHackathon\Home\Api\Service\Event;

use SaHackathon\Home\Api\Service\SaverInterface;

abstract class EventSaverService implements SaverInterface
{
    /**
     * Validate if all neccessary data are present.
     *
     * @param array $data
     *
     * @return bool
     */
    protected function areValid(array $data)
    {
        $requiredKeys = [
            'title', // article title
            'decision', // like, dislike, skip
            'time', // duration
            'user', // user id
            'hash', // article id
        ];

        foreach ($requiredKeys as $key) {
            if (!isset($data[$key]) || empty($data[$key])) {
                return false;
            }
        }

        return true;
    }
}
