<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 14/03/19
 * Time: 11:07.
 */

namespace Grr\GrrBundle\Message;

class SmsNotification
{
    /**
     * @var string
     */
    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }
}
