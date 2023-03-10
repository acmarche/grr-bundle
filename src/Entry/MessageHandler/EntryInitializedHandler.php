<?php

namespace Grr\GrrBundle\Entry\MessageHandler;

use Grr\Core\Entry\Message\EntryInitialized;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EntryInitializedHandler
{
    public function __construct()
    {
    }

    public function __invoke(EntryInitialized $entryCreated): void
    {
    }
}
