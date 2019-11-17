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

use Grr\GrrBundle\Repository\CategoryRepository;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function home()
    {
        $categories = $this->categoryRepository->findAll();

        return $this->render('@Grr/default/index.html.twig', ['categories' => $categories]);
    }

    public function t()
    {
        $email = (new NotificationEmail())
            ->from('fabien@example.com')
            ->to('fabien@example.org')
            ->subject('My first notification email via Symfony')
            ->markdown(
                <<<EOF
There is a **problem** on your website, you should investigate it right now.
Or just wait, the problem might solves itself automatically, we never know.
EOF
            )
            ->action('More info?', 'https://example.com/')
            ->importance('high')//->exception(new \LogicException('That does not work at all...'))
        ;
    }

}