<?php
/**
 * Tag Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Tests\WebBaseTestCase;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class TagControllerTest.
 */
class TagControllerTest extends WebBaseTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/tag';

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * testIndexRouteAnonymousUser.
     */
    public function testIndexRouteAnonymousUser(): void
    {
        // given
        $user = null;
        $expectedStatusCode = 301;
        try {
            $user = $this->createUser([UserRole::ROLE_ADMIN->value], 'tagindexuser1@example.com');
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
        $expectedStatusCode = 301;
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'tag_user@example.com');
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
        $user = $this->createUser([UserRole::ROLE_USER->value], 'tag_user2@example.com');
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(301, $resultStatusCode);
    }

    /**
     * Test show single tag.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testShowTag(): void
    {
        // given
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'tag_user2@exmaple.com');
        $this->httpClient->loginUser($adminUser);

        $expectedTag = new Tag();
        $expectedTag->setTitle('Test tag');
        $expectedTag->setCreatedAt(new \DateTimeImmutable('now'));
        $expectedTag->setUpdatedAt(new \DateTimeImmutable('now'));
        $tagRepository = static::getContainer()->get(TagRepository::class);
        $tagRepository->save($expectedTag);

        // when
        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/'.$expectedTag->getId());
        $result = $this->httpClient->getResponse();

        // then
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_OK, $result->getStatusCode());
        $this->assertSelectorTextContains('th', 'Id');
        // ... more assertions...
    }

    // create tag

    /**
     * @throws OptimisticLockException
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws ContainerExceptionInterface
     */
    public function testCreateTag(): void
    {
        // given
        $user = $this->createUser(
            [UserRole::ROLE_USER->value],
            'tag_created_user2@example.com'
        );
        $this->httpClient->loginUser($user);
        $tagTagTitle = 'createdTag';
        $tagTagSlug = 'slag';
        $tagRepository = static::getContainer()->get(TagRepository::class);

        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/new');
        // when
        $this->httpClient->submitForm(
            'Zapisz',
            ['tag' => ['title' => $tagTagTitle, 'slug' => $tagTagSlug]]
        );

        // then
        $savedTag = $tagRepository->findOneByTitle($tagTagTitle);
        $this->assertEquals(
            $tagTagTitle,
            $savedTag->getTitle()
        );

        $result = $this->httpClient->getResponse();
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_SEE_OTHER, $result->getStatusCode());
    }

    /**
     * testEditTag.
     */
    public function testEditTag(): void
    {
        // given
        $user = $this->createUser(
            [UserRole::ROLE_USER->value],
            'tag_edit_user1@example.com'
        );
        $this->httpClient->loginUser($user);

        $tagRepository =
            static::getContainer()->get(TagRepository::class);
        $testTag = new Tag();
        $testTag->setTitle('TestTag');
        $testTag->setCreatedAt(new \DateTimeImmutable('now'));
        $testTag->setUpdatedAt(new \DateTimeImmutable('now'));
        $tagRepository->save($testTag);
        $testTagId = $testTag->getId();
        $expectedNewTagTitle = 'TestTagEdit';

        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/'.
        $testTagId.'/edit');

        // when
        $this->httpClient->submitForm(
            'Edytuj',
            ['tag' => ['title' => $expectedNewTagTitle]]
        );

        // then
        $savedTag = $tagRepository->findOneById($testTagId);
        $this->assertEquals(
            $expectedNewTagTitle,
            $savedTag->getTitle()
        );
    }

    /**
     * testNewRoutAdminUser.
     */
    public function testNewRoutAdminUser(): void
    {
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'tagCreate1@example.com');
        $this->httpClient->loginUser($adminUser);
        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/');
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_OK, $this->httpClient->getResponse()->getStatusCode());
    }

    /**
     * testDeleteTag.
     */
    public function testDeleteTag(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(
                [UserRole::ROLE_USER->value],
                'tag_deleted_user1@example.com'
            );
        } catch (OptimisticLockException|ORMException|NotFoundExceptionInterface|ContainerExceptionInterface) {
        }
        $this->httpClient->loginUser($user);

        $tagRepository =
            static::getContainer()->get(TagRepository::class);
        $testTag = new Tag();
        $testTag->setTitle('TestTagCreated');
        $testTag->setCreatedAt(new \DateTimeImmutable('now'));
        $testTag->setUpdatedAt(new \DateTimeImmutable('now'));
        $tagRepository->save($testTag);
        $testTagId = $testTag->getId();

        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/'.$testTagId.'/delete');

        // when
        $this->httpClient->submitForm(
            'Usuń'
        );

        // then
        $this->assertNull($tagRepository->findOneByTitle('TestTagCreated'));
    }

    /*
     * @return void
     */
    //    public function testCantDeleteTag(): void
    //    {
    //        // given
    //        $user = null;
    //        try {
    //            $user = $this->createUser([UserRole::ROLE_USER->value],
    //                'tag_deleted_user2@example.com');
    //        } catch (OptimisticLockException|ORMException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
    //        }
    //        $this->httpClient->loginUser($user);
    //
    //        $tagRepository =
    //            static::getContainer()->get(TagRepository::class);
    //        $testTag = new Tag();
    //        $testTag->setTitle('TestTagCreated2');
    //        $testTag->setCreatedAt(new \DateTimeImmutable('now'));
    //        $testTag->setUpdatedAt(new \DateTimeImmutable('now'));
    //        $tagRepository->save($testTag);
    //        $testTagId = $testTag->getId();
    //
    //        $this->createSong($user, $testTag);
    //
    //        //when
    //        $this->httpClient->request('GET', self::TEST_ROUTE . '/' . $testTagId . '/delete');
    //
    //        // then
    //        $this->assertEquals(302, $this->httpClient->getResponse()->getStatusCode());
    //        $this->assertNotNull($tagRepository->findOneByTitle('TestTagCreated2'));
    //    }
}
