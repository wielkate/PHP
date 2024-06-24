<?php

namespace App\Tests\Forms;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\Form\Test\TypeTestCase;

class CategoryTypeTest extends TypeTestCase
{
    public function testSubmitValidDate()
    {
        $time = new \DateTimeImmutable('now');
        $formatData = [
            'title' => 'TestCategory',
            'createdAt' => $time,
            'updatedAt' => $time,
        ];

        $model = new Category();
        $form = $this->factory->create(CategoryType::class, $model);

        $expected = new Category();
        $expected->setTitle('TestCategory');
        $expected->setCreatedAt($time);
        $expected->setUpdatedAt($time);
        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected->getTitle(), $model->getTitle());
        $this->assertEquals($expected->getId(), $model->getId());
    }
}
