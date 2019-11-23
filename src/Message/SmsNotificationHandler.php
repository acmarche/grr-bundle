<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 14/03/19
 * Time: 11:09.
 */

namespace Grr\GrrBundle\Message;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SmsNotificationHandler implements MessageHandlerInterface
{
    public function __invoke(SmsNotification $message): void
    {
    }
}
