<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 18/10/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailFactory
{
    public static function createNewTemplated(): TemplatedEmail
    {
        return new TemplatedEmail();
    }
}
