<?php
/**
 * This file is part of sf5 application
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 16/11/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Grr\GrrBundle\Controller;

use Grr\Core\Repository\AreaRepositoryInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @var AreaRepositoryInterface
     */
    private $areaRepository;
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(AreaRepositoryInterface $areaRepository, MailerInterface $mailer)
    {
        $this->areaRepository = $areaRepository;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function home()
    {
        $categories = $this->areaRepository->findAll();

        return $this->render('@Grr/default/index.html.twig', ['categories' => $categories]);
    }

    public function t()
    {
        $email = (new NotificationEmail())
            ->from('fabien@marche.be')
            ->to('jf@marche.be')
            ->cc('jfsenechal@gmail.com')
            ->subject('My first notification email via Symfony')
            ->markdown(
                <<<EOF
There is a **problem** on your website, you should investigate it right now.
Or just wait, the problem might solves itself automatically, we never know.
EOF
            )
            ->action('More info2?', 'https://example.com/')
        ;
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            dump($e->getMessage());
        }
    }

}