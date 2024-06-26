<?php
/**
 * UserRole test.
 */

namespace App\Tests\Entity\Enum;

use App\Entity\Enum\UserRole;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UserRoleTest.
 */
class UserRoleTest extends WebTestCase
{
    /**
     * test label.
     */
    public function testLabel()
    {
        self::assertEquals('label.role_user', UserRole::ROLE_USER->label());
        self::assertEquals('label.role_admin', UserRole::ROLE_ADMIN->label());
    }
}
