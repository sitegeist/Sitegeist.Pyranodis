<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;

/**
 * @implements \IteratorAggregate<int,SchemaOrgProperty>
 */
#[Flow\Proxy(false)]
class SchemaOrgProperties implements \IteratorAggregate
{
    /**
     * @var array<int,SchemaOrgProperty>
     */
    private readonly array $properties;

    public function __construct(SchemaOrgProperty ...$properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return \ArrayIterator<int,SchemaOrgProperty>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->properties);
    }
}
