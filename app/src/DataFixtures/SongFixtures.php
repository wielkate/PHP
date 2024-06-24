<?php
/**
 * Song fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Song;
use Faker\Factory;
use Random\RandomException;

/**
 * Song fixtures.
 */
class SongFixtures extends AbstractBaseFixtures
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

        for ($i = 0; $i < 20; ++$i) {
            $song = new Song();
            $song->setTitle($faker->words(random_int(1, 6), true));
            $song->setCategory($this->getRandomReference('categories'));
            $song->setCreatedAt(
                \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $song->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $duration = \DateTime::createFromFormat('H:i:s', '00:0'.random_int(1, 9).':'.random_int(0, 5).random_int(0, 9));
            $song->setDuration($duration);
            $song->setComment($faker->text(30));

            $this->manager->persist($song);
        }

        $this->manager->flush();
    }
}
