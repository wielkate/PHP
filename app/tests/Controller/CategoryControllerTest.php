<?php
/**
 * Category Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Repository\CategoryRepository;
use App\Tests\WebBaseTestCase;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class CategoryControllerTest.
 */
class CategoryControllerTest extends WebBaseTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/category';

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
        $expectedStatusCode = 200;
        try {
            $user = $this->createUser([UserRole::ROLE_ADMIN->value], 'categoryindexuser1@example.com');
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
        $adminUser = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'category_user@example.com');
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
        $user = $this->createUser([UserRole::ROLE_USER->value], 'category_user2@example.com');
        $this->httpClient->loginUser($user);

        // when
        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE);
        $resultStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultStatusCode);
    }

    /**
     * Test show single category.
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    public function testShowCategory(): void
    {
        // given
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'category_user2@exmaple.com');
        $this->httpClient->loginUser($adminUser);

        $expectedCategory = new Category();
        $expectedCategory->setTitle('Test category');
        $expectedCategory->setCreatedAt(new \DateTimeImmutable('now'));
        $expectedCategory->setUpdatedAt(new \DateTimeImmutable('now'));
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);
        $categoryRepository->save($expectedCategory);

        // when
        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/'.$expectedCategory->getId());
        $result = $this->httpClient->getResponse();

        // then
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_OK, $result->getStatusCode());
        $this->assertSelectorTextContains('th', 'Id');
        // ... more assertions...
    }

    // create category

    /**
     * @throws OptimisticLockException
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws ContainerExceptionInterface
     */
    public function testCreateCategory(): void
    {
        // given
        $user = $this->createUser(
            [UserRole::ROLE_USER->value],
            'category_created_user2@example.com'
        );
        $this->httpClient->loginUser($user);
        $categoryCategoryTitle = 'createdCategor';
        $categoryCategorySlug = 'createdSlug';
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);

        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/new');
        // when
        $this->httpClient->submitForm(
            'Zapisz',
            ['category' => ['title' => $categoryCategoryTitle, 'slug' => $categoryCategorySlug]]
        );

        // then
        $savedCategory = $categoryRepository->findOneByTitle($categoryCategoryTitle);
        $this->assertEquals(
            $categoryCategoryTitle,
            $savedCategory->getTitle()
        );

        $result = $this->httpClient->getResponse();
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_FOUND, $result->getStatusCode());
    }

    /**
     * testEditCategory.
     */
    public function testEditCategory(): void
    {
        // given
        $user = $this->createUser(
            [UserRole::ROLE_USER->value],
            'category_edit_user1@example.com'
        );
        $this->httpClient->loginUser($user);

        $categoryRepository =
            static::getContainer()->get(CategoryRepository::class);
        $testCategory = new Category();
        $testCategory->setTitle('TestCategory');
        $testCategory->setCreatedAt(new \DateTimeImmutable('now'));
        $testCategory->setUpdatedAt(new \DateTimeImmutable('now'));
        $categoryRepository->save($testCategory);
        $testCategoryId = $testCategory->getId();
        $expectedNewCategoryTitle = 'TestCategoryEdit';

        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/'.
        $testCategoryId.'/edit');

        // when
        $this->httpClient->submitForm(
            'Edytuj',
            ['category' => ['title' => $expectedNewCategoryTitle]]
        );

        // then
        $savedCategory = $categoryRepository->findOneById($testCategoryId);
        $this->assertEquals(
            $expectedNewCategoryTitle,
            $savedCategory->getTitle()
        );
    }

    /**
     * testNewRoutAdminUser.
     */
    public function testNewRoutAdminUser(): void
    {
        $adminUser = $this->createUser([UserRole::ROLE_ADMIN->value, UserRole::ROLE_USER->value], 'categoryCreate1@example.com');
        $this->httpClient->loginUser($adminUser);
        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/');
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_MOVED_PERMANENTLY, $this->httpClient->getResponse()->getStatusCode());
    }

    /**
     * testDeleteCategory.
     */
    public function testDeleteCategory(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(
                [UserRole::ROLE_USER->value],
                'category_deleted_user1@example.com'
            );
        } catch (OptimisticLockException|ORMException|NotFoundExceptionInterface|ContainerExceptionInterface) {
        }
        $this->httpClient->loginUser($user);

        $categoryRepository =
            static::getContainer()->get(CategoryRepository::class);
        $testCategory = new Category();
        $testCategory->setTitle('TestCategoryCreated');
        $testCategory->setCreatedAt(new \DateTimeImmutable('now'));
        $testCategory->setUpdatedAt(new \DateTimeImmutable('now'));
        $categoryRepository->save($testCategory);
        $testCategoryId = $testCategory->getId();

        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/'.$testCategoryId.'/delete');

        // when
        $this->httpClient->submitForm(
            'Usuń'
        );

        // then
        $this->assertNull($categoryRepository->findOneByTitle('TestCategoryCreated'));
    }

    /**
     * testCantDeleteCategory.
     */
    public function testCantDeleteCategory(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(
                [UserRole::ROLE_USER->value],
                'category_deleted_user2@example.com'
            );
        } catch (OptimisticLockException|ORMException|NotFoundExceptionInterface|ContainerExceptionInterface) {
        }
        $this->httpClient->loginUser($user);

        $categoryRepository =
            static::getContainer()->get(CategoryRepository::class);
        $testCategory = new Category();
        $testCategory->setTitle('TestCategoryCreated2');
        $testCategory->setCreatedAt(new \DateTimeImmutable('now'));
        $testCategory->setUpdatedAt(new \DateTimeImmutable('now'));
        $categoryRepository->save($testCategory);
        $testCategoryId = $testCategory->getId();

        $this->createSong($user, $testCategory);

        // when
        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/'.$testCategoryId.'/delete');

        // then
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_FOUND, $this->httpClient->getResponse()->getStatusCode());
        $this->assertNotNull($categoryRepository->findOneByTitle('TestCategoryCreated2'));
    }
}
