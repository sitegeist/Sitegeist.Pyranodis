<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
final readonly class SchemaOrgClass
{
    /**
     * @param array<int,string> $parentClassIds
     */
    public function __construct(
        public string $id,
        public string $comment,
        public string $label,
        public array $parentClassIds,
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
            array_key_exists('rdfs:subClassOf', $jsonArray)
                ? array_map(
                    fn (array $domainInclusion): string => \mb_substr($domainInclusion['@id'], 7),
                    array_key_exists('@id', $jsonArray['rdfs:subClassOf'])
                        ? [$jsonArray['rdfs:subClassOf']]
                        : $jsonArray['rdfs:subClassOf']
                )
                : []
        );
    }
}
