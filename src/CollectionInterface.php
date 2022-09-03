<?php

declare(strict_types=1);

namespace Guennichi\Collection;

use Closure;
use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * @api
 *
 * @template TValue
 *
 * @extends IteratorAggregate<int, TValue>
 */
interface CollectionInterface extends IteratorAggregate, Countable, JsonSerializable
{
    /**
     * @return array<int, TValue>
     */
    public function toArray(): array;

    /**
     * @return TValue|null
     */
    public function first(): mixed;

    /**
     * @return TValue|null
     */
    public function last(): mixed;

    /**
     * @param Closure(TValue, int): void $closure
     */
    public function each(Closure $closure): void;

    /**
     * Check if all elements of the collection satisfy the given predicate.
     *
     * @param Closure(TValue, int): bool $predicate
     */
    public function forAll(Closure $predicate): bool;

    /**
     * @return CollectionInterface<TValue>
     */
    public function reverse(): self;

    public function isEmpty(): bool;

    /**
     * @return CollectionInterface<TValue>
     */
    public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): self;

    /**
     * @param Closure(TValue): mixed $closure
     *
     * @return CollectionInterface<TValue>
     */
    public function sortAscBy(Closure $closure): self;

    /**
     * @param Closure(TValue): mixed $closure
     *
     * @return CollectionInterface<TValue>
     */
    public function sortDescBy(Closure $closure): self;

    /**
     * @param CollectionInterface<TValue> $that
     *
     * @return CollectionInterface<TValue>
     */
    public function merge(self $that): self;

    /**
     * @template T
     *
     * @param class-string<CollectionInterface<T>> $collectionClassname
     * @param Closure(TValue): T $closure
     *
     * @return CollectionInterface<T>
     */
    public function mapTo(string $collectionClassname, Closure $closure): self;

    /**
     * @param Closure(TValue, int): bool $closure
     *
     * @return CollectionInterface<TValue>
     */
    public function filter(Closure $closure): self;

    /**
     * @param TValue $element
     */
    public function contains(mixed $element): bool;

    /**
     * @param TValue $element
     */
    public function indexOf(mixed $element): int|false;
}
