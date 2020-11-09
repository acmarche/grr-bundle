<?php

namespace Grr\GrrBundle\Notification;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Notifier\Channel\BrowserChannel;
use Symfony\Component\Notifier\Channel\ChannelInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;

/**
 * Ajout de la propriete "type" pour success, danger,...
 *
 * @see BrowserChannel
 * Class BrowserGrrChannel.
 */
final class BrowserGrrChannel implements ChannelInterface
{
    private $stack;

    public function __construct(RequestStack $stack)
    {
        $this->stack = $stack;
    }

    public function notify(Notification $notification, Recipient $recipient, string $transportName = null): void
    {
        if (null === $request = $this->stack->getCurrentRequest()) {
            return;
        }

        $message = $notification->getSubject();
        if ($notification->getEmoji()) {
            $message = $notification->getEmoji().' '.$message;
        }

        $type = $notification->getType() ?? 'success';
        $request->getSession()->getFlashBag()->add($type, $message);
    }

    public function supports(Notification $notification, Recipient $recipient): bool
    {
        return true;
    }
}
