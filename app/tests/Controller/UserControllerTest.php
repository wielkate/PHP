<?php
/**
 * User Controller test.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserControllerTest.
 */
class UserControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $passwordEncoder;

    /**
     * SetUp.
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::$container->get('doctrine')->getManager();
        $this->passwordEncoder = self::$container->get(UserPasswordEncoderInterface::class);
    }

    /**
     * Test change password action.
     */
    public function testChangePassword()
    {
        // Load a user from the database or create a new user
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail('test@example.com');

        if (!$user) {
            $user = new User();
            $user->setEmail('test@example.com');
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'old_password'));
            $user->setRoles(['ROLE_USER']);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        // Simulate logging in as the user
        $this->client->loginUser($user);

        // Make a request to the change password page
        $crawler = $this->client->request('GET', '/user/change-password');

        // Assert the response status code is successful
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Select the form and submit it
        $form = $crawler->selectButton('Change Password')->form([
            'change_password[current_password]' => 'old_password',
            'change_password[new_password][first]' => 'new_password',
            'change_password[new_password][second]' => 'new_password',
        ]);

        $this->client->submit($form);

        // Assert a successful redirect after form submission
        $this->assertTrue($this->client->getResponse()->isRedirect('/'));

        // Follow the redirect and assert a flash message is present
        $crawler = $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success', 'Password changed successfully');

        // Optionally, assert that the password was actually changed in the database
        $this->entityManager->refresh($user);
        $this->assertTrue($this->passwordEncoder->isPasswordValid($user, 'new_password'));
    }

    /**
     * Test index action.
     */
    public function testIndex()
    {
        // Make a request to the index page
        $crawler = $this->client->request('GET', '/user');

        // Assert the response status code is successful
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // Optionally, assert that a specific element exists on the page
        $this->assertSelectorTextContains('h1', 'User List');
    }
}
