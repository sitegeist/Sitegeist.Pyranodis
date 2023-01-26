<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;

/**
 * @implements \IteratorAggregate<string,SchemaOrgProperties>
 */
#[Flow\Proxy(false)]
class GroupedSchemaOrgProperties implements \IteratorAggregate
{
    public function __construct(
        /** @var array<string,SchemaOrgProperties> */
        private readonly array $properties
    ) {
    }

    /**
     * @see SchemaOrgProperties::reduceToManageable()
     */
    public function reduceToManageable(
        array $declaredPropertyTypes,
        array $declaredNodeTypes
    ): self {
        return new self(
            array_map(
                fn (SchemaOrgProperties $properties): SchemaOrgProperties => $properties->reduceToManageable(
                    $declaredPropertyTypes,
                    $declaredNodeTypes
                ),
                $this->properties
            )
        );
    }

    public function getDifference(self $other): self
    {
        $difference = [];
        foreach ($this->properties as $className => $properties) {
            $otherProperties = $other->getByClassName($className);
            $difference[$className] = $otherProperties
                ? $properties->getDifference($otherProperties)
                : $properties;
        }

        return new self($difference);
    }

    public function getByClassName(string $className): ?SchemaOrgProperties
    {
        return $this->properties[$className] ?? null;
    }

    public function getById(string $id): ?SchemaOrgProperty
    {
        foreach ($this->properties as $properties) {
            $property = $properties->getById($id);
            if ($property) {
                return $property;
            }
        }

        return null;
    }

    /**
     * @return array<int,SchemaOrgProperty>
     */
    public function getAllProperties(): array
    {
        $allProperties = [];
        foreach ($this->properties as $properties) {
            $allProperties = array_merge($allProperties, $properties->getIterator()->getArrayCopy());
        }

        return $allProperties;
    }

    /**
     * @return \ArrayIterator<string,SchemaOrgProperties>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->properties);
    }
}
