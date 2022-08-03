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
     */
    public function __construct(
        public readonly string $id,
        public readonly string $comment,
        public readonly string $label,
        public readonly array $domainIncludes
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
            )
        );
    }

    public function isMemberOfClass(string $className): bool
    {
        return in_array($className, $this->domainIncludes);
    }
}
