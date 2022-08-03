<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;

/**
 * @implements \IteratorAggregate<int,SchemaOrgClass>
 */
#[Flow\Proxy(false)]
class SchemaOrgClasses implements \IteratorAggregate
{
    /**
     * @var array<int,SchemaOrgClass>
     */
    private readonly array $classes;

    public function __construct(SchemaOrgClass ...$classes)
    {
        $this->classes = $classes;
    }

    /**
     * @return \ArrayIterator<int,SchemaOrgClass>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->classes);
    }
}
