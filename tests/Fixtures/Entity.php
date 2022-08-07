<?php

declare(strict_types=1);

namespace Tests\Guennichi\Collection\Fixtures;

final class Entity
{
    public function __construct(public readonly string $property)
    {
    }
}
