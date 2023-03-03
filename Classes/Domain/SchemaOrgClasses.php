<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;

/**
 * @implements \IteratorAggregate<int|string,SchemaOrgClass>
 */
#[Flow\Proxy(false)]
class SchemaOrgClasses implements \IteratorAggregate
{
    /**
     * @var array<int|string,SchemaOrgClass>
     */
    private readonly array $classes;

    public function __construct(SchemaOrgClass ...$classes)
    {
        $this->classes = $classes;
    }

    public function getById(string $id): ?SchemaOrgClass
    {
        foreach ($this->classes as $class) {
            if ($class->id === $id) {
                return $class;
            }
        }

        return null;
    }

    /**
     * @return \ArrayIterator<int|string,SchemaOrgClass>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->classes);
    }
}
