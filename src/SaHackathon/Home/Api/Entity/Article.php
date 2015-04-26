<?php

namespace SaHackathon\Home\Api\Entity;

class Article {

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $decision;

    /**
     * @var float
     */
    private $time;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $date;

    /**
     * @var int
     */
    private $likes;

    /**
     * @var int
     */
    private $dislikes;

    /**
     * @var int
     */
    private $skips;

    /**
     * @return int
     */
    public function getSkips()
    {
        return $this->skips;
    }

    /**
     * @param int $skips
     * @return $this
     */
    public function setSkips($skips)
    {
        $this->skips = $skips;

        return $this;
    }

    /**
     * @var float
     */
    private $timeLikes;

    /**
     * @var float
     */
    private $timeDislikes;

    /**
     * @var float
     */
    private $timeSkip;

    /**
     * Sets default date for article
     */
    public function __construct()
    {
        $this->setDate(date('Y-m-d H:i:s'));

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDecision()
    {
        return $this->decision;
    }

    /**
     * @param mixed $decision
     * @return $this
     */
    public function setDecision($decision)
    {
        $this->decision = $decision;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     * @return $this
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     * @return $this
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param mixed $likes
     * @return $this
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDislikes()
    {
        return $this->dislikes;
    }

    /**
     * @param mixed $dislikes
     * @return $this
     */
    public function setDislikes($dislikes)
    {
        $this->dislikes = $dislikes;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeDislikes()
    {
        return $this->timeDislikes;
    }

    /**
     * @param mixed $timeDislikes
     * @return $this
     */
    public function setTimeDislikes($timeDislikes)
    {
        $this->timeDislikes = $timeDislikes;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeLikes()
    {
        return $this->timeLikes;
    }

    /**
     * @param mixed $timeLikes
     * @return $this
     */
    public function setTimeLikes($timeLikes)
    {
        $this->timeLikes = $timeLikes;

        return $this;
    }

    /**
     * @return float
     */
    public function getTimeSkip()
    {
        return $this->timeSkip;
    }

    /**
     * @param float $timeSkip
     * @return $this
     */
    public function setTimeSkip($timeSkip)
    {
        $this->timeSkip = $timeSkip;

        return $this;
    }

}
