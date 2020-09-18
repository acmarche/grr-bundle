<?php


namespace Grr\GrrBundle\Notification\Message;


class NotificationUpdated
{
    /**
     * @var int
     */
    private $preferenceId;

    public function __construct(int $preferenceId)
    {
        $this->preferenceId = $preferenceId;
    }

    public function getEnfantId(): int
    {
        return $this->preferenceId;
    }
}
