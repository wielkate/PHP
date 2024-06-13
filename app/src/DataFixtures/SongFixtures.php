<?php
/**
 * Song fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Song;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class SongFixtures.
 */
class SongFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullPropertyFetch
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(100, 'songs', function (int $i) {
            $song = new Song();
            $song->setTitle($this->faker->sentence);
            $song->setCreatedAt(
                DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $song->setUpdatedAt(
                DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );

            $duration = DateTime::createFromFormat('H:i:s', '00:0'.random_int(1,9).':'.random_int(0,5).random_int(0,9));
            $song->setDuration($duration);
            $song->setComment($this->faker->text(30));

            /** @var Category $category */
            $category = $this->getRandomReference('categories');
            $song->setCategory($category);

            return $song;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: CategoryFixtures::class}
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}
