<?php

namespace App\Tests;

use App\Entity\Category;
use App\Entity\Song;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\SongRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class WebBaseTestCase extends WebTestCase
{
    /**
     * Test client.
     */
    protected KernelBrowser $httpClient;

    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @return User User entity
     */
    protected function createUser(array $roles, string $email): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setEmail($email);
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
     * Simulate user log in.
     *
     * @param User $user User entity
     */
    protected function logIn(User $user): void
    {
        $session = self::getContainer()->get('session');

        $firewallName = 'main';
        $firewallContext = 'main';

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->httpClient->getCookieJar()->set($cookie);
    }

    /**
     * Create category.
     */
    protected function createCategory(): Category
    {
        $category = new Category();
        $category->setTitle('TName');
        $category->setUpdatedAt(new \DateTimeImmutable('now'));
        $category->setCreatedAt(new \DateTimeImmutable('now'));
        $categoryRepository = self::getContainer()->get(CategoryRepository::class);
        $categoryRepository->save($category);

        return $category;
    }

    /**
     * Create Tag.
     */
    protected function createTag(): Tag
    {
        $tag = new Tag();
        $tag->setTitle('TTag');
        $tag->setCreatedAt(\DateTimeImmutable::createFromFormat('Y-m-d', '2021-05-09'));
        $tag->setUpdatedAt(\DateTimeImmutable::createFromFormat('Y-m-d', '2021-05-09'));
        $tagRepository = self::getContainer()->get(TagRepository::class);
        $tagRepository->save($tag);

        return $tag;
    }

    /**
     * @param User     $user     param
     * @param Category $category param
     *
     * @return void return
     */
    protected function createSong(User $user, Category $category): void
    {
        $Song = new Song();
        $Song->setTitle('TName');
        $Song->setCreatedAt(\DateTimeImmutable::createFromFormat('Y-m-d', '2021-05-09'));
        $Song->setUpdatedAt(\DateTimeImmutable::createFromFormat('Y-m-d', '2021-05-09'));
        $Song->setDuration(\DateTime::createFromFormat('H:i:s', '00:03:00'));
        $Song->setComment('test comment');
        $Song->setCategory($category);
        $Song->addTag($this->createTag());

        $SongRepository = self::getContainer()->get(SongRepository::class);
        $SongRepository->save($Song);
    }
}
