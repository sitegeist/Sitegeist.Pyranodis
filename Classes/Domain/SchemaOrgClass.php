<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
class SchemaOrgClass
{
    /**
     * @param array<int,string> $parentClassIds
     */
    public function __construct(
        public readonly string $id,
        public readonly string $comment,
        public readonly string $label,
        public readonly array $parentClassIds,
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
                array_key_exists('@id', $jsonArray['rdfs:subClassOf'])
                    ? [$jsonArray['rdfs:subClassOf']]
                    : $jsonArray['rdfs:subClassOf']
            )
        );
    }
}
