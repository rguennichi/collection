<?php

declare(strict_types=1);

namespace Guennichi\Collection;

use Closure;
use Traversable;

/**
 * @template TValue
 *
 * @implements CollectionInterface<TValue>
 */
class Collection implements CollectionInterface
{
    /**
     * @var array<int, TValue>
     */
    private readonly array $elements;

    /**
     * @param TValue ...$elements
     */
    public function __construct(mixed ...$elements)
    {
        $this->elements = array_values($elements);
    }

    /**
     * @return array<int, array<TValue>|TValue>
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this as $key => $element) {
            $array[$key] = $element instanceof CollectionInterface ? $element->toArray() : $element;
        }

        return $array;
    }

    /**
     * @return TValue|null
     */
    public function first(): mixed
    {
        return empty($elements = $this->elements) ? null : reset($elements);
    }

    /**
     * @return TValue|null
     */
    public function last(): mixed
    {
        return empty($elements = $this->elements) ? null : end($elements);
    }

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * @param Closure(TValue, int): void $closure
     */
    public function each(Closure $closure): void
    {
        foreach ($this as $key => $element) {
            $closure($element, $key);
        }
    }

    /**
     * @param Closure(TValue, int): bool $predicate
     */
    public function every(Closure $predicate): bool
    {
        foreach ($this as $key => $element) {
            if (true !== $predicate($element, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return CollectionInterface<TValue>
     */
    public function reverse(): CollectionInterface
    {
        return new static(...array_reverse($this->elements));
    }

    /**
     * @param Closure(TValue): mixed $closure
     *
     * @return CollectionInterface<TValue>
     */
    public function sortAscBy(Closure $closure): CollectionInterface
    {
        $sorted = $this->elements;

        usort($sorted, static fn (mixed $a, mixed $b): int => $closure($a) <=> $closure($b));

        return new static(...$sorted);
    }

    /**
     * @param Closure(TValue): mixed $closure
     *
     * @return CollectionInterface<TValue>
     */
    public function sortDescBy(Closure $closure): CollectionInterface
    {
        $sorted = $this->elements;

        usort($sorted, static fn (mixed $a, mixed $b): int => $closure($b) <=> $closure($a));

        return new static(...$sorted);
    }

    /**
     * @param CollectionInterface<TValue> $that
     *
     * @return CollectionInterface<TValue>
     */
    public function merge(CollectionInterface $that): CollectionInterface
    {
        return new static(...[...$this->elements, ...$that->toArray()]);
    }

    /**
     * @template T
     *
     * @param class-string<CollectionInterface<T>> $collectionClassname
     * @param Closure(TValue): T $closure
     *
     * @return CollectionInterface<T>
     */
    public function mapTo(string $collectionClassname, Closure $closure): CollectionInterface
    {
        if (!is_subclass_of($collectionClassname, self::class)) {
            throw new \TypeError(sprintf('"%s" should be an instance of "%s"', $collectionClassname, self::class));
        }

        $mapped = [];
        foreach ($this as $key => $element) {
            $mapped[$key] = $closure($element);
        }

        return new $collectionClassname(...$mapped);
    }

    /**
     * @param Closure(TValue, int): bool $closure
     *
     * @return CollectionInterface<TValue>
     */
    public function filter(Closure $closure): CollectionInterface
    {
        $filtered = [];
        foreach ($this as $key => $element) {
            if (true === $closure($element, $key)) {
                $filtered[] = $element;
            }
        }

        return new static(...$filtered);
    }

    /**
     * @param TValue $element
     */
    public function contains(mixed $element): bool
    {
        return \in_array($element, $this->elements, true);
    }

    /**
     * @param TValue $element
     */
    public function indexOf(mixed $element): int|false
    {
        return array_search($element, $this->elements, true);
    }

    /**
     * @return Traversable<int, TValue>
     */
    public function getIterator(): Traversable
    {
        yield from $this->elements;
    }

    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * @return CollectionInterface<TValue>
     */
    public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): CollectionInterface
    {
        return new static(...\array_slice($this->elements, $offset, $length, $preserveKeys));
    }

    /**
     * @return array<int, array<TValue>|TValue>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
