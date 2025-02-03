<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony;

use ApiPlatform\State\Pagination\PaginatorInterface;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * Wrapper around PaginatorInterface to be used with ApiPlatform after
 *   the results are propertly converted from Doctrine objects into
 *   suitable, DTO objects.
 *
 * ArrayPaginator could be used, this just prepares the pagination process
 *   for further customizations.
 */
class ApiPlatformPaginator implements PaginatorInterface, IteratorAggregate
{
    public function __construct(
        private iterable $items,
        private int $totalItems,
        private int $currentPage,
        private int $itemsPerPage,
    ) {
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function getItems(): iterable
    {
        return $this->items;
    }

    public function count(): int
    {
        return $this->totalItems;
    }

    public function getCurrentPage(): float
    {
        return $this->currentPage;
    }

    public function getItemsPerPage(): float
    {
        return $this->itemsPerPage;
    }

    public function getLastPage(): float
    {
        return $this->itemsPerPage > 0 ? ceil($this->totalItems / $this->itemsPerPage) : 1;
    }

    public function getTotalItems(): float
    {
        return $this->totalItems;
    }
}
