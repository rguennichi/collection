<?php

declare(strict_types=1);

namespace Tests\Guennichi\Collection\Fixtures;

use Guennichi\Collection\Collection;

/**
 * @extends Collection<Entity>
 */
final class MappedEntityCollection extends Collection
{
    public function __construct(Entity ...$elements)
    {
        parent::__construct(...$elements);
    }
}
