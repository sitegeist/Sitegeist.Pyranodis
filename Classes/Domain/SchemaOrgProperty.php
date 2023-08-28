<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final readonly class SchemaOrgProperty implements \Stringable
{
    /**
     * @param array<int,string> $domainIncludes
     * @param array<int,string> $rangeIncludes
     */
    public function __construct(
        public string $id,
        public string $comment,
        public string $label,
        public array $domainIncludes,
        public array $rangeIncludes
    ) {
    }

    /**
     * @param array<string,mixed> $jsonArray
     */
    public static function fromSchemaOrgJsonArray(array $jsonArray): self
    {
        return new self(
            \mb_substr($jsonArray['@id'], 7),
            is_array($jsonArray['rdfs:comment'])
                ? $jsonArray['rdfs:comment']['@value']
                : $jsonArray['rdfs:comment'],
            is_array($jsonArray['rdfs:label'])
                ? $jsonArray['rdfs:label']['@value']
                : $jsonArray['rdfs:label'],
            array_key_exists('schema:domainIncludes', $jsonArray)
                ? array_map(
                    fn (array $domainInclusion): string => \mb_substr($domainInclusion['@id'], 7),
                    array_key_exists('@id', $jsonArray['schema:domainIncludes'])
                        ? [$jsonArray['schema:domainIncludes']]
                        : $jsonArray['schema:domainIncludes']
                )
                : [],
            array_key_exists('schema:rangeIncludes', $jsonArray)
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
        return array_unique(array_map(
            fn (string $schemaOrgType): string => match ($schemaOrgType) {
                'Integer' => 'integer',
                'Date', 'Time', 'DateTime' => 'DateTime',
                'Boolean' => 'boolean',
                'Number' => 'float',
                'ImageObject' => 'Neos\Media\Domain\Model\ImageInterface',
                default => 'string'
            },
            $this->rangeIncludes
        ));
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
