<?php

declare(strict_types=1);

namespace Tests\Guennichi\Collection;

use PHPUnit\Framework\TestCase;
use Tests\Guennichi\Collection\Fixtures\Entity;
use Tests\Guennichi\Collection\Fixtures\EntityCollection;
use Tests\Guennichi\Collection\Fixtures\MappedEntityCollection;

class CollectionTest extends TestCase
{
    public function testToArray(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $array = $collection->toArray();

        $this->assertIsArray($array);
        $this->assertCount(3, $array);
    }

    public function testFirst(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $this->assertEquals(new Entity('example1'), $collection->first());

        $collection = new EntityCollection();

        $this->assertNull($collection->first());
    }

    public function testLast(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $this->assertEquals(new Entity('example3'), $collection->last());

        $collection = new EntityCollection();

        $this->assertNull($collection->last());
    }

    public function testEach(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $names = [];
        $collection->each(static function (Entity $entity, int $index) use (&$names) {
            $names[$index] = $entity->property;
        });

        $this->assertSame(['example1', 'example2', 'example3'], $names);

        $this->assertSame([0, 1, 2], array_keys($names));
    }

    public function testForAll(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $this->assertFalse($collection->forAll(fn (Entity $entity) => 'example' === $entity->property));

        $collection = new EntityCollection(
            new Entity('example'),
            new Entity('example'),
            new Entity('example'),
        );

        $this->assertTrue($collection->forAll(fn (Entity $entity) => 'example' === $entity->property));
    }

    public function testReverse(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $this->assertEquals(new EntityCollection(
            new Entity('example3'),
            new Entity('example2'),
            new Entity('example1'),
        ), $collection->reverse());

        $this->assertNotSame($collection->reverse(), $collection);
    }

    public function testIsEmpty(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $this->assertFalse($collection->isEmpty());

        $collection = new EntityCollection();

        $this->assertTrue($collection->isEmpty());
    }

    public function testSlice(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $this->assertEquals(new EntityCollection(new Entity('example3')), $collection->slice(2));
        $this->assertEquals(new EntityCollection(), $collection->slice(5, 50));
        $this->assertNotSame($collection, $collection->slice(1));
    }

    public function testSort(): void
    {
        $collection = new EntityCollection(
            new Entity('example3'),
            new Entity('example1'),
            new Entity('example2'),
        );

        $this->assertEquals(new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        ), $collection->sortAscBy(static fn (Entity $entity) => $entity->property));

        $this->assertEquals(new EntityCollection(
            new Entity('example3'),
            new Entity('example2'),
            new Entity('example1'),
        ), $collection->sortDescBy(static fn (Entity $entity) => $entity->property));
    }

    public function testMerge(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $mergedCollection = $collection->merge(new EntityCollection(new Entity('example4'), new Entity('example5')));

        $this->assertEquals(new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
            new Entity('example4'),
            new Entity('example5'),
        ), $mergedCollection);

        $this->assertNotSame($mergedCollection, $collection);
    }

    public function testMap(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $mappedCollection = $collection->mapTo(
            MappedEntityCollection::class,
            static fn (Entity $entity) => new Entity($entity->property . 'Mapped'),
        );

        $this->assertInstanceOf(MappedEntityCollection::class, $mappedCollection);

        $this->assertEquals(new MappedEntityCollection(
            new Entity('example1Mapped'),
            new Entity('example2Mapped'),
            new Entity('example3Mapped'),
        ), $mappedCollection);
    }

    public function testFilter(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $filteredCollection = $collection->filter(static fn (Entity $entity, int $key) => \in_array($key, [1, 2]));

        $this->assertEquals(new EntityCollection(
            new Entity('example2'),
            new Entity('example3'),
        ), $filteredCollection);

        $this->assertNotSame($filteredCollection, $collection);
    }

    public function testContains(): void
    {
        $element1 = new Entity('example1');

        $collection = new EntityCollection(
            $element1,
            new Entity('example2'),
            new Entity('example3'),
        );

        $this->assertTrue($collection->contains($element1));
        $this->assertFalse($collection->contains(new Entity('example1'))); // should be strict
    }

    public function testIndexOf(): void
    {
        $element2 = new Entity('example2');

        $collection = new EntityCollection(
            new Entity('example1'),
            $element2,
            new Entity('example3'),
        );

        $this->assertSame(1, $collection->indexOf($element2));
        $this->assertFalse($collection->indexOf(new Entity('example2')));
    }

    public function testJsonSerialize(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $this->assertSame('[{"property":"example1"},{"property":"example2"},{"property":"example3"}]', json_encode($collection));
    }

    public function testCount(): void
    {
        $collection = new EntityCollection(
            new Entity('example1'),
            new Entity('example2'),
            new Entity('example3'),
        );

        $this->assertEquals(3, \count($collection));
    }
}
