<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Tests\Unit\Domain;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Sitegeist\Pyranodis\Domain\SchemaOrgProperty;

class SchemaOrgPropertyTest extends TestCase
{
    /**
     * @dataProvider propertyProvider
     * @param array<string,mixed> $sample
     */
    public function testFromSchemaOrgJsonArray(array $sample, SchemaOrgProperty $expectedProperty): void
    {
        Assert::assertEquals($expectedProperty, SchemaOrgProperty::fromSchemaOrgJsonArray($sample));
    }

    /**
     * @return array<int,array<int,mixed>|SchemaOrgProperty>
     */
    public function propertyProvider(): array
    {
        return [
            [
                [
                    '@id' => 'schema:postalCode',
                    '@type' => 'rdf:Property',
                    'rdfs:comment' => 'The postal code. For example, 94043.',
                    'rdfs:label' => 'postalCode',
                    'schema:domainIncludes' => [
                        [
                            '@id' => 'schema:PostalAddress'
                        ] ,
                        [
                            '@id' => 'schema:DefinedRegion'
                        ] ,
                        [
                            '@id' => 'schema:GeoCoordinates'
                        ] ,
                        [
                            '@id' => 'schema:GeoShape'
                        ]
                    ] ,
                    'schema:rangeIncludes' => [
                        '@id' => 'schema:Text'
                    ],
                    'schema:source' => [
                        '@id' => 'https://github.com/schemaorg/schemaorg/issues/2506'
                    ]
                ],
                new SchemaOrgProperty(
                    'postalCode',
                    'The postal code. For example, 94043.',
                    'postalCode',
                    [
                        'PostalAddress',
                        'DefinedRegion',
                        'GeoCoordinates',
                        'GeoShape'
                    ],
                    [
                        'Text'
                    ]
                )
            ],
            [
                [
                    '@id' => 'schema:ingredients',
                    '@type' => 'rdf:Property',
                    'rdfs:comment' => 'A single ingredient used in the recipe, e.g. sugar, flour or garlic.',
                    'rdfs:label' => 'ingredients',
                    'rdfs:subPropertyOf' => [
                        '@id' => 'schema:supply'
                    ],
                    'schema:domainIncludes' => [
                        '@id' => 'schema:Recipe'
                    ],
                    'schema:rangeIncludes' => [
                        '@id' => 'schema:Text'
                    ],
                    'schema:supersededBy' => [
                        '@id' => 'schema:recipeIngredient'
                    ]
                ],
                new SchemaOrgProperty(
                    'ingredients',
                    'A single ingredient used in the recipe, e.g. sugar, flour or garlic.',
                    'ingredients',
                    [
                        'Recipe'
                    ],
                    [
                        'Text'
                    ]
                )
            ]
        ];
    }
}
