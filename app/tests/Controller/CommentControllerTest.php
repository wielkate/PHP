<?php
/**
 * Comment Controller test.
 */

namespace App\Tests\Controller;

use App\Entity\Comment;
use App\Entity\Enum\UserRole;
use App\Repository\CommentRepository;
use App\Tests\WebBaseTestCase;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class CommentControllerTest.
 */
class CommentControllerTest extends WebBaseTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/comment';

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    // create comment

    /**
     * @throws OptimisticLockException
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws ContainerExceptionInterface
     */
    public function testCreateComment(): void
    {
        // given
        $user = $this->createUser(
            [UserRole::ROLE_USER->value],
            'comment_created_user2@example.com'
        );
        $this->httpClient->loginUser($user);
        $commentCommentTitle = 'createdCategor';
        $commentCommentSlug = 'createdSlug';
        $commentRepository = static::getContainer()->get(CommentRepository::class);
        $song = $this->createSong($user, $this->createCategory());

        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/song/'.$song->getId().'/new');
        // when
        $this->httpClient->submitForm(
            'Zapisz',
            ['comment' => ['text' => $commentCommentTitle]]
        );

        // then
        $savedComment = $commentRepository->findOneByText($commentCommentTitle);
        $this->assertEquals(
            $commentCommentTitle,
            $savedComment->getText()
        );

        $result = $this->httpClient->getResponse();
        $this->assertEquals(\Symfony\Component\HttpFoundation\Response::HTTP_FOUND, $result->getStatusCode());
    }

    /**
     * testDeleteComment.
     */
    public function testDeleteComment(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(
                [UserRole::ROLE_ADMIN->value],
                'comment_deleted_user1@example.com'
            );
        } catch (OptimisticLockException|ORMException|NotFoundExceptionInterface|ContainerExceptionInterface) {
        }
        $this->httpClient->loginUser($user);

        $commentRepository =
            static::getContainer()->get(CommentRepository::class);
        $testComment = new Comment();
        $testComment->setText('TestCommentCreated');
        $testComment->setCreatedAt(new \DateTimeImmutable('now'));
        $testComment->setUpdatedAt(new \DateTimeImmutable('now'));
        $testComment->setSong($this->createSong($user, $this->createCategory()));
        $commentRepository->save($testComment);
        $testCommentId = $testComment->getId();

        $this->httpClient->request(\Symfony\Component\HttpFoundation\Request::METHOD_GET, self::TEST_ROUTE.'/'.$testCommentId.'/delete');

        // when
        $this->httpClient->submitForm(
            'Usuń'
        );

        // then
        $this->assertNull($commentRepository->findOneByText('TestCommentCreated'));
    }
}
