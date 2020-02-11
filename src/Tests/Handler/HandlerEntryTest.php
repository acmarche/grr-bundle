<?php
/**
 * This file is part of GrrSf application.
 *
 * @author jfsenechal <jfsenechal@gmail.com>
 * @date 22/08/19
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Grr\GrrBundle\Tests\Handler;

use Grr\GrrBundle\Entry\HandlerEntry;
use Grr\Core\Tests\BaseTesting;

/**
 * todo test  handler
 * Class HandlerEntryTest.
 */
class HandlerEntryTest extends BaseTesting
{
    public function testBidon(): void
    {
        self::assertTrue(true);
    }

    protected function initHandler(): HandlerEntry
    {
        return new HandlerEntry();
    }
}
