<?php
/**
 * Song fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Song;
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
        $this->createMany(20, 'songs', function ($i) {
            $song = new Song();
            $song->setTitle($this->faker->words(random_int(1, 6), true));
            $song->setCategory($this->getRandomReference('categories'));
            $song->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $song->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $duration = \DateTime::createFromFormat('H:i:s', '00:0'.random_int(1, 9).':'.random_int(0, 5).random_int(0, 9));
            $song->setDuration($duration);

            $this->addReference('song_'.$i, $song); // Dodajemy referencję do piosenki

            return $song; // Zwróć utworzony obiekt
        });

        $this->manager->flush();
    }
}
