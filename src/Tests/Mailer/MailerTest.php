<?php

namespace Grr\GrrBundle\Tests\Mailer;

use Grr\Core\Mailer\GrrMailer;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class MailerTest extends KernelTestCase
{
    //https://symfony.com/blog/new-in-symfony-4-4-phpunit-assertions-for-email-messages
    public function testSomething()
    {
        $mailerInterface = $this->createMock(MailerInterface::class);
        $mailerInterface
            ->expects($this->once())
            ->method('send');

        $pdf = $this->createMock(Pdf::class);
        $twig = $this->createMock(Environment::class);

        $grrMailer = new GrrMailer($mailerInterface, $twig, $pdf);
        $email = $grrMailer->sendTest();

        $this->assertSame('Your weekly report on the Space Bar!', $email->getSubject());
        $this->assertCount(1, $email->getTo());
        /** @var Address[] $namedAddresses */
        $namedAddresses = $email->getTo();
        $this->assertInstanceOf(Address::class, $namedAddresses[0]);
        $this->assertSame('zeze', $namedAddresses[0]->getName());
        $this->assertSame('jf@marche.be', $namedAddresses[0]->getAddress());

        /*
         * Symfony 4.4:
         * $this->assertEmailCount(1);
         * $email = $this->getMailerMessage(0);
         * $this->assertEmailHeaderSame($email, 'To', 'fabien@symfony.com');
         */
    }

    public function testIntegra2tionSendAuthorWeeklyReportMessage()
    {
        self::bootKernel();
        $mailerInterface = $this->getMockBuilder(MailerInterface::class)->getMock();
        $mailerInterface
            ->expects($this->once())
            ->method('send');

        $pdf = self::$container->get(Pdf::class);
        $twig = self::$container->get(Environment::class);

        $grrMailer = new GrrMailer($mailerInterface, $twig, $pdf);
        $email = $grrMailer->sendTest();
        $this->assertCount(1, $email->getAttachments());
    }
}
