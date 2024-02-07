<?php

namespace Grr\GrrBundle\Notification;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Notifier\Channel\BrowserChannel;
use Symfony\Component\Notifier\Channel\ChannelInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

/**
 * Ajout de la propriete "type" pour success, danger,...
 *
 * @see BrowserChannel
 * Class BrowserGrrChannel.
 */
final readonly class BrowserGrrChannel implements ChannelInterface
{
    public function __construct(
        private RequestStack $stack
    ) {
    }

    public function notify(Notification $notification, RecipientInterface $recipient, string $transportName = null): void
    {
        if (null === $request = $this->stack->getCurrentRequest()) {
            return;
        }

        $message = $notification->getSubject();
        if ('' !== $notification->getEmoji()) {
            $message = $notification->getEmoji().' '.$message;
        }

        $type = $notification->getType() ?? 'success';
        $request->getSession()->getFlashBag()->add($type, $message);
    }

    public function supports(Notification $notification, RecipientInterface $recipient): bool
    {
        return true;
    }
}
