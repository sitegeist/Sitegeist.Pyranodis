<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\ContentRepository\Core\NodeType\NodeType;
use Neos\Flow\Annotations as Flow;

/**
 * @implements \IteratorAggregate<int|string,SchemaOrgProperty>
 */
#[Flow\Proxy(false)]
class SchemaOrgProperties implements \IteratorAggregate
{
    /**
     * @var array<int|string,SchemaOrgProperty>
     */
    private readonly array $properties;

    public function __construct(SchemaOrgProperty ...$properties)
    {
        $this->properties = $properties;
    }

    public function getDifference(self $other): self
    {
        return new self(...array_diff(
            $this->properties,
            $other->properties
        ));
    }

    /**
     * Reduces the set of schema.org properties to the ones manageable by the Neos UI or value objects
     * @param array<string,mixed> $declaredPropertyTypes
     * @param array<int,NodeType> $declaredNodeTypes
     */
    public function reduceToManageable(
        array $declaredPropertyTypes,
        array $declaredNodeTypes
    ): self {
        $properties = [];
        $manageablePropertyTypes = array_merge(
            [
                'Text',
                'Boolean',
                'Number',
                'Integer',
                'URL',
                'Date',
                'DateTime',
                'MediaObject',
                'ImageObject',
                'AudioObject',
                'VideoObject'
            ],
            array_map(
                fn (string $propertyType): string => \mb_substr($propertyType, \mb_strrpos($propertyType, '\\') + 1),
                array_keys($declaredPropertyTypes)
            ),
            array_map(
                fn (string $nodeTypeName): string => \mb_substr($nodeTypeName, \mb_strrpos($nodeTypeName, '.') + 1),
                array_map(
                    fn (NodeType $nodeType): string => $nodeType->getName(),
                    $declaredNodeTypes
                )
            ),
            array_filter(array_map(
                fn (NodeType $nodeType): ?string => $nodeType->getOptions()['schemaOrgClassName'] ?? null,
                $declaredNodeTypes
            ))
        );

        foreach ($this->properties as $property) {
            foreach ($property->rangeIncludes as $type) {
                if (in_array($type, $manageablePropertyTypes)) {
                    $properties[] = $property;
                    continue 2;
                }
            }
        }

        return new self(...$properties);
    }

    public function getById(string $id): ?SchemaOrgProperty
    {
        foreach ($this->properties as $property) {
            if ($property->id === $id) {
                return $property;
            }
        }

        return null;
    }

    public function getByIndex(int $index): ?SchemaOrgProperty
    {
        return $this->properties[$index] ?? null;
    }

    /**
     * @return \ArrayIterator<int|string,SchemaOrgProperty>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->properties);
    }

    public function isEmpty(): bool
    {
        return empty($this->properties);
    }
}
