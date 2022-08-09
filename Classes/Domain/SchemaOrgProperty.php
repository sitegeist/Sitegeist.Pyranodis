<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
class SchemaOrgProperty
{
    /**
     * @param array<int,string> $domainIncludes
     * @param array<int,string> $rangeIncludes
     */
    public function __construct(
        public readonly string $id,
        public readonly string $comment,
        public readonly string $label,
        public readonly array $domainIncludes,
        public readonly array $rangeIncludes
    ) {
    }

    /**
     * @param array<string,mixed> $jsonArray
     * @return static
     */
    public static function fromSchemaOrgJsonArray(array $jsonArray): self
    {
        return new self(
            \mb_substr($jsonArray['@id'], 7),
            $jsonArray['rdfs:comment'],
            $jsonArray['rdfs:label'],
            array_map(
                fn (array $domainInclusion): string => \mb_substr($domainInclusion['@id'], 7),
                array_key_exists('@id', $jsonArray['schema:domainIncludes'])
                    ? [$jsonArray['schema:domainIncludes']]
                    : $jsonArray['schema:domainIncludes']
            ),
            isset($jsonArray['schema:rangeIncludes'])
                ? array_map(
                fn (array $rangeIncludes): string => \mb_substr($rangeIncludes['@id'], 7),
                array_key_exists('@id', $jsonArray['schema:rangeIncludes'])
                    ? [$jsonArray['schema:rangeIncludes']]
                    : $jsonArray['schema:rangeIncludes']
                )
                : []
        );
    }

    public function isMemberOfClass(string $className): bool
    {
        return in_array($className, $this->domainIncludes);
    }

    /**
     * @return array<int,string>
     * @todo add value object support
     */
    public function getTypeSuggestions(): array
    {
        return array_filter(array_map(
            fn (string $schemaOrgType): ?string => match($schemaOrgType) {
                'Text' => 'string',
                'Integer' => 'integer',
                'Date', 'Time', 'DateTime' => '\DateTime',
                'Boolean' => 'boolean',
                'Number' => 'float',
                default => null
            },
            $this->rangeIncludes
        ));
    }
}
