<?php
/**
 * Category fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;

/**
 * Category fixtures.
 */
class CategoryFixtures extends AbstractBaseFixtures
{
    /**
     * @return void return
     */
    public function loadData(): void
    {
        $this->createMany(20, 'categories', function ($i) {
            $category = new Category();
            $category->setTitle($this->faker->words(1, true));
            $category->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $category->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $this->manager->persist($category);

            return $category;
        });

        $this->manager->flush();
    }
}
