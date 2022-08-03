<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Tests\Unit\Domain;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Sitegeist\Pyranodis\Domain\SchemaOrgClass;

class SchemaOrgClassTest extends TestCase
{
    /**
     * @dataProvider classProvider
     * @param array<string,mixed> $sample
     */
    public function testFromSchemaOrgJsonArray(array $sample, SchemaOrgClass $expectedClass): void
    {
        Assert::assertEquals($expectedClass, SchemaOrgClass::fromSchemaOrgJsonArray($sample));
    }

    /**
     * @return array<int,array<int,mixed>|SchemaOrgClass>
     */
    public function classProvider(): array
    {
        return [
            [
                [
                    '@id' => 'schema:Product',
                    '@type' => 'rdfs:Class',
                    'rdfs:comment' => 'Any offered product or service. For example: a pair of shoes; a concert ticket; the rental of a car; a haircut; or an episode of a TV show streamed online.',
                    'rdfs:label' => 'Product',
                    'rdfs:subClassOf' => [
                        '@id' => 'schema:Thing'
                    ],
                    'schema:source' => [
                        '@id' => 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_GoodRelationsTerms'
                    ]
                ],
                new SchemaOrgClass(
                    'Product',
                    'Any offered product or service. For example: a pair of shoes; a concert ticket; the rental of a car; a haircut; or an episode of a TV show streamed online.',
                    'Product',
                    [
                        'Thing'
                    ]
                )
            ],
            [
                [
                    '@id' => 'schema:HowToSection',
                    '@type' => 'rdfs:Class',
                    'rdfs:comment' => 'A sub-grouping of steps in the instructions for how to achieve a result (e.g. steps for making a pie crust within a pie recipe).',
                    'rdfs:label' => 'HowToSection',
                    'rdfs:subClassOf' => [
                        [
                            '@id' => 'schema:ListItem'
                        ],
                        [
                            '@id' => 'schema:CreativeWork'
                        ],
                        [
                            '@id' => 'schema:ItemList'
                        ]
                    ]
                ],
                new SchemaOrgClass(
                    'HowToSection',
                    'A sub-grouping of steps in the instructions for how to achieve a result (e.g. steps for making a pie crust within a pie recipe).',
                    'HowToSection',
                    [
                        'ListItem',
                        'CreativeWork',
                        'ItemList'
                    ]
                )
            ]
        ];
    }
}
