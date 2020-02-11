<?php

namespace Grr\GrrBundle\Tests\Mailer;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MailerControllerTest extends WebTestCase
{
    public function testSomething()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Hello World');

        /* Symfony 4.4:
        $this->assertEmailCount(1);
        $email = $this->getMailerMessage(0);
        $this->assertEmailHeaderSame($email, 'To', 'fabien@symfony.com');
        */
    }
}
