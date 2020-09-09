<?php

namespace Grr\GrrBundle\Entry\Message;

final class EntryCreated
{
    /**
     * @var int
     */
    private $entryId;

    public function __construct(int $entryId)
    {
        $this->entryId = $entryId;
    }

    public function getEntryId(): int
    {
        return $this->entryId;
    }
}
