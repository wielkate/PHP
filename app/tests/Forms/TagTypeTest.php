<?php

// namespace App\Tests\Forms;
//
// use App\Entity\Tag;
// use App\Form\TagType;
// use DateTime;
// use Symfony\Component\Form\Test\TypeTestCase;
//
// class TagTypeTest extends TypeTestCase
// {
//    /**
//     * @return void
//     */
//    public function testSubmitValidDate()
//    {
//        $time = new \DateTimeImmutable('now');
//        $formatData = [
//            'title' => 'TestTag',
//            'createdAt' => $time,
//            'updatedAt' => $time
//        ];
//
//        $model = new Tag();
//        $form = $this->factory->create(TagType::class, $model);
//
//        $expected = new Tag();
//        $expected->setTitle('TestTag');
//        $expected->setCreatedAt($time);
//        $expected->setUpdatedAt($time);
//        $form->submit($formatData);
//
//        $this->assertTrue($form->isSynchronized());
//        $this->assertEquals($expected->getTitle(), $model->getTitle());
//        $this->assertEquals($expected->getId(), $model->getId());
//        $this->assertEquals($expected->getCreatedAt(), $model->getCreatedAt());
//        $this->assertEquals($expected->getUpdatedAt(), $model->getUpdatedAt());
//    }

// }
