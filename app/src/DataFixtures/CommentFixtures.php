<?php
/**
 * Comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Random\RandomException;

/**
 * Comment fixtures.
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * LoadData.
     *
     * @return void return
     *
     * @throws RandomException throw
     */
    public function loadData(): void
    {
        $faker = Factory::create();

        $this->createMany(20, 'comments', function ($i) {
            $comment = new Comment();
            $comment->setText($this->faker->words(random_int(1, 6), true));
            $comment->setSong($this->getRandomReference('songs'));
            $comment->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $comment->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );

            return $comment; // Zwróć utworzony obiekt
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixture classes
     * on which the implementing class depends on.
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            SongFixtures::class,
        ];
    }
}
