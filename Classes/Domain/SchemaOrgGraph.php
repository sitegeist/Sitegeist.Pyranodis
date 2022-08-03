<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;
use Neos\Utility\Files;

#[Flow\Proxy(false)]
class SchemaOrgGraph
{
    public function __construct(
        private readonly SchemaOrgClasses $classes,
        private readonly SchemaOrgProperties $properties
    ) {
    }

    public static function createFromRemoteResource(): self
    {
        return self::fromSchemaOrgJsonString(
            Files::getFileContents('https://schema.org/version/latest/schemaorg-current-https.jsonld')
        );
    }

    public static function createFromLocalResource(): self
    {
        return self::fromSchemaOrgJsonString(
            Files::getFileContents('resource://Sitegeist.Pyranodis/Private/Data/schemaorg-current-https.jsonld')
        );
    }

    public static function fromSchemaOrgJsonString(string $jsonString): self
    {
        $schemas = \json_decode(
            $jsonString,
            true,
            JSON_THROW_ON_ERROR
        );

        return self::fromSchemaOrgJsonArray($schemas['@graph']);
    }

    /**
     * @param array<int,array<string,mixed>> $jsonArray
     */
    public static function fromSchemaOrgJsonArray(array $jsonArray): self
    {
        $classes = [];
        $properties = [];
        foreach ($jsonArray as $node) {
            $type = SchemaOrgType::tryFrom($node['@type']);
            if ($type === SchemaOrgType::TYPE_CLASS) {
                $classes[] = SchemaOrgClass::fromSchemaOrgJsonArray($node);
            } elseif ($type === SchemaOrgType::TYPE_PROPERTY) {
                $properties[] = SchemaOrgProperty::fromSchemaOrgJsonArray($node);
            }
        }

        return new self(
            new SchemaOrgClasses(...$classes),
            new SchemaOrgProperties(...$properties)
        );
    }

    public function getPropertiesForClassName(string $className): SchemaOrgProperties
    {
        return new SchemaOrgProperties();
    }
}