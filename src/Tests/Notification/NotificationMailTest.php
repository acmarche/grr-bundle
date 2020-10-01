<?php

namespace Grr\GrrBundle\Tests\Notification;

use Grr\Core\Tests\BaseTesting;

class NotificationMailTest extends BaseTesting
{
    public function setNotificationMailTest()
    {
        $url = '/fr/admin/area/';
        $email = 'grr@domain.be';

        $client = !$email ? $this->createAnonymousClient() : $this->createGrrClient($email);
        $client->request('GET', $url);
        self::assertResponseStatusCodeSame(200, $email.' '.$url);
    }

    public function testSomething()
    {
        $client = $this->createGrrClient('grr@domain.be');
        $crawler = $client->request('GET', '/');
        $crawler = $client->followRedirect();
        $crawler = $client->followRedirect();

        $links = $crawler->filter('#entries-data a')->each(
            function ($node) {
                $href = $node->attr('href');
                $title = $node->attr('title');
                $text = $node->text();
                print_r($title);

                return compact('href');
            }
        );
        var_dump($links);
        $links = $crawler->filter('#entries-data a');
        var_dump($links->count());

      //  print_r($client->getResponse()->getContent());

        $link = $crawler->filter('#entries-data a[title="Ajouter une réservation"]');

        return;
        $client->click($link->link());
     //   print_r($client->getResponse()->getContent());
        $crawler = $client->click($crawler->selectLink('15')->link());
        // print_r($client->getResponse()->getContent());

        $crawler = $crawler->filter('#sign-up')->link();

        $crawler = $client->click($client->selectLink('Ajouter une réservation')->link());

        $form = $crawler->selectButton('Mettre à jour')->form(
            [
                'entry_with_periodicity[name]' => '123456',
            ]
        );

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('html:contains("123456")')->count());

        print_r($client->getResponse()->getContent());
        /*   $this->assertEmailIsQueued($this->getMailerEvent(0));

           $email = $this->getMailerMessage(0);
           $this->assertEmailHeaderSame($email, 'To', 'fabien@symfony.com');
           $this->assertEmailTextBodyContains($email, 'Welcome to Symfony!');
           $this->assertEmailCount(1);
           // $this->assertEmailAttachementCount($email, 1);*/
    }
}
