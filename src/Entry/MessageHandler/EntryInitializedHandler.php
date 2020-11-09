<?php

namespace Grr\GrrBundle\Entry\MessageHandler;

use Grr\Core\Entry\Message\EntryInitialized;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EntryInitializedHandler implements MessageHandlerInterface
{
    public function __construct()
    {
    }

    public function __invoke(EntryInitialized $entryCreated): void
    {
    }
}
