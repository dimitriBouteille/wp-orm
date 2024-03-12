<?php

namespace Dbout\WpOrm\Tests\WordPress\Models;

use Dbout\WpOrm\Models\User;

/**
 * @coversDefaultClass \Dbout\WpOrm\Models\User
 */
class UserTest extends \WP_UnitTestCase
{
    /**
     * @return void
     * @covers ::findOneByEmail
     */
    public function testFindOneByLogin(): void
    {
        $user = User::findOneByEmail('test@test.fr');
        $this->assertInstanceOf(User::class, $user);
    }
}