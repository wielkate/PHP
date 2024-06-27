<?php
/**
 * Tag fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Class TagFixtures.
 */
class TagFixtures extends Fixture
{
    public $faker;
    //    /**
    //     * Faker.
    //     *
    //     * @var \Generator
    //     */
    //    protected \Generator $faker;

    /**
     * Persistence object manager.
     */
    protected ObjectManager $manager;

    /**
     * Load.
     *
     * @param ObjectManager $manager Persistence object manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 20; ++$i) {
            $tag = new Tag();
            $tag->setTitle($this->faker->words(1, true));
            $tag->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $tag->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $manager->persist($tag);
        }

        $manager->flush();
    }
}
