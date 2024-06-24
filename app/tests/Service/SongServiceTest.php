<?php
/**
 * SongService tests.
 */

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Enum\UserRole;
use App\Entity\Tag;
use App\Entity\Song;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use App\Repository\SongRepository;
use App\Repository\UserRepository;
use App\Service\SongService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class SongServiceTest.
 */
class SongServiceTest extends KernelTestCase
{
    public $songService;
    /**
     * @var object
     */
    public $categoryRepository;
    /**
     * @var object
     */
    public $tagRepository;
    /**
     * @var object
     */
    public $userRepository;
    /**
     * Song service.
     */
    private ?SongService $SongService;

    /**
     * Song repository.
     */
    private ?SongRepository $songRepository;

    /**
     * Test save.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function testSave(): void
    {
        // given
        $expectedSong = new Song();
        $expectedSong->setTitle('Test Song');
        $expectedSong->setUpdatedAt(new \DateTimeImmutable('now'));
        $expectedSong->setCreatedAt(new \DateTimeImmutable('now'));
        $expectedSong->setDuration(\DateTime::createFromFormat('H:i:s', '00:01:59'));
        $expectedSong->setComment('Test comment');
        $expectedSong->setCategory($this->createCategory());
        $expectedSong->addTag($this->createTag());
        //        try {
        //            $expectedSong->setAuthor($user = $this->createUser([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value], 'user999@example.com'));
        //        } catch (OptimisticLockException|ORMException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        //        }

        // when
        $this->songService->save($expectedSong);
        $resultSong = $this->songRepository->findOneById(
            $expectedSong->getId()
        );

        // then
        $this->assertEquals($expectedSong, $resultSong);
    }

    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @return User User entity
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    protected function createUser(array $roles, string $email): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setEmail($email);
        //        $user->setCreatedAt(new \DateTimeImmutable('now'));
        //        $user->setUpdatedAt(new \DateTimeImmutable('now'));
        $user->setRoles($roles);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'p@55w0rd'
            )
        );
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->save($user);

        return $user;
    }

    /**
     * Create Category.
     */
    private function createCategory(): Category
    {
        $category = new Category();
        $category->setTitle('TCategory');
        $category->setUpdatedAt(new \DateTimeImmutable('now'));
        $category->setCreatedAt(new \DateTimeImmutable('now'));
        $categoryRepository = self::getContainer()->get(CategoryRepository::class);
        $categoryRepository->save($category);

        return $category;
    }

    /**
     * Create Tag.
     */
    private function createTag(): Tag
    {
        $tag = new Tag();
        $tag->setTitle('TTag');
        $tag->setUpdatedAt(new \DateTimeImmutable('now'));
        $tag->setCreatedAt(new \DateTimeImmutable('now'));
        $tagRepository = self::getContainer()->get(TagRepository::class);
        $tagRepository->save($tag);

        return $tag;
    }

    /**
     * Set up test.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->songRepository = $container->get(SongRepository::class);
        $this->songService = $container->get(SongService::class);
        $this->categoryRepository = $container->get(CategoryRepository::class);
        $this->tagRepository = $container->get(TagRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
    }
}
