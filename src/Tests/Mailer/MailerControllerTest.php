<?php

namespace Grr\GrrBundle\Tests\Mailer;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MailerControllerTest extends WebTestCase
{
    public function te2stSomething()
    {
        /* Symfony 4.4:
        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);
        $this->assertEmailHeaderSame($email, 'To', 'fabien@symfony.com');
        */
    }
}
