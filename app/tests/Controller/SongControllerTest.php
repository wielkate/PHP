<?php
/**
 * Song Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\Song;
use App\Repository\SongRepository;
use App\Tests\WebBaseTestCase;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SongControllerTest.
 */
class SongControllerTest extends WebBaseTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/song';

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * TestIndexRouteAnonymousUser.
     */
    public function testIndexRouteAnonymousUser(): void
    {
        // given
        $user = null;
        $expectedStatusCode = 200;
        try {
            $user = $this->createUser([UserRole::ROLE_ADMIN->value], 'songindexuser1@example.com');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface|ORMException) {
        }
        // $this->logIn($user);
        $this->httpClient->loginUser($user);
        // when
        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for admin user.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testIndexRouteAdminUser(): void
    {
        // given
        $expectedStatusCode = 200;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'song_user@example.com');
        $this->httpClient->loginUser($adminUser);

        // when
        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedStatusCode, $resultStatusCode);
    }

    /**
     * Test index route for non-authorized user.
     */
    public function testIndexRouteNonAuthorizedUser(): void
    {
        // given
        $user = $this->createUser([UserRole::ROLE_USER->value], 'song_user2@example.com');
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultStatusCode);
    }

    /**
     * Test show single song.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testShowSong(): void
    {
        // given
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'song_user2@exmaple.com');
        $this->httpClient->loginUser($adminUser);

        $expectedSong = new Song();
        $expectedSong->setTitle('Test song');
        $expectedSong->setCreatedAt(new \DateTimeImmutable('now'));
        $expectedSong->setUpdatedAt(new \DateTimeImmutable('now'));
        $expectedSong->setDuration(new \DateTime('00:03:00'));
        $songRepository = static::getContainer()->get(SongRepository::class);
        $songRepository->save($expectedSong);

        // when
        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/'.$expectedSong->getId());
        $result = $this->httpClient->getResponse();

        // then
        $this->assertEquals(Response::HTTP_OK, $result->getStatusCode());
        $this->assertSelectorTextContains('th', 'ID');
        // ... more assertions...
    }

    /**
     * @throws OptimisticLockException
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws ContainerExceptionInterface
     */
    public function testEditSong(): void
    {
        // given
        $user = $this->createUser(
            [UserRole::ROLE_ADMIN->value],
            'song_edit_user1@example.com'
        );
        $this->httpClient->loginUser($user);

        $songRepository =
            static::getContainer()->get(SongRepository::class);
        $testSong = new Song();
        $testSong->setTitle('TestSong');
        $testSong->setCreatedAt(new \DateTimeImmutable('now'));
        $testSong->setUpdatedAt(new \DateTimeImmutable('now'));
        $testSong->setDuration(new \DateTime('00:03:00'));
        $songRepository->save($testSong);
        $testSongId = $testSong->getId();
        $expectedNewSongTitle = 'TestSongEdit';

        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/'.
        $testSongId.'/edit');

        // when
        $this->httpClient->submitForm(
            'Edytuj',
            ['song' => ['title' => $expectedNewSongTitle]]
        );

        // then
        $savedSong = $songRepository->findOneById($testSongId);
        $this->assertEquals(
            $expectedNewSongTitle,
            $savedSong->getTitle()
        );
    }

    /**
     * Test New Rout Admin User.
     */
    public function testNewRoutAdminUser(): void
    {
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'songCreate1@example.com');
        $this->httpClient->loginUser($adminUser);
        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/');
        $this->assertEquals(Response::HTTP_MOVED_PERMANENTLY, $this->httpClient->getResponse()->getStatusCode());
    }
}
