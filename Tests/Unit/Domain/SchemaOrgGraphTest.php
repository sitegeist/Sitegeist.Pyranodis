<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Tests\Unit\Domain;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Sitegeist\Pyranodis\Domain\GroupedSchemaOrgProperties;
use Sitegeist\Pyranodis\Domain\SchemaOrgClass;
use Sitegeist\Pyranodis\Domain\SchemaOrgClasses;
use Sitegeist\Pyranodis\Domain\SchemaOrgGraph;
use Sitegeist\Pyranodis\Domain\SchemaOrgProperties;
use Sitegeist\Pyranodis\Domain\SchemaOrgProperty;

class SchemaOrgGraphTest extends TestCase
{
    /**
     * @dataProvider serializedGraphProvider
     * @param array<string,mixed> $sample
     */
    public function testFromSchemaOrgJsonArray(array $sample, SchemaOrgGraph $expectedClass): void
    {
        Assert::assertEquals($expectedClass, SchemaOrgGraph::fromSchemaOrgJsonArray($sample));
    }

    /**
     * @return array<int,array<int,array<string,mixed>|SchemaOrgGraph>>
     */
    public function serializedGraphProvider(): array
    {
        return [
            [
                [
                    [
                        '@id' => 'schema:BroadcastChannel',
                        '@type' => 'rdfs:Class',
                        'rdfs:comment' => 'A unique instance of a BroadcastService on a CableOrSatelliteService lineup.',
                        'rdfs:label' => 'BroadcastChannel',
                        'rdfs:subClassOf' => [
                            '@id' => 'schema:Intangible'
                        ]
                    ],
                    [
                        '@id' => 'schema:Thursday',
                        '@type' => 'schema:DayOfWeek',
                        'rdfs:comment' => 'The day of the week between Wednesday and Friday.',
                        'rdfs:label' => 'Thursday',
                        'schema:sameAs' => [
                            '@id' => 'http://www.wikidata.org/entity/Q129'
                        ]
                    ],
                    [
                        '@id' => 'schema:musicGroupMember',
                        '@type' => 'rdf:Property',
                        'rdfs:comment' => 'A member of a music group&#x2014;for example, John, Paul, George, or Ringo.',
                        'rdfs:label' => 'musicGroupMember',
                        'schema:domainIncludes' => [
                            '@id' => 'schema:MusicGroup'
                        ],
                        'schema:rangeIncludes' => [
                            '@id' => 'schema:Person'
                        ],
                        'schema:supersededBy' => [
                            '@id' => 'schema:member'
                        ]
                    ],
                    [
                        '@id' => 'schema:UnclassifiedAdultConsideration',
                        '@type' => 'schema:AdultOrientedEnumeration',
                        'rdfs:comment' => 'The item is suitable only for adults, without indicating why. Due to widespread use of \'adult\' as a euphemism for \'sexual\', many such items are likely suited also for the SexualContentConsideration code.',
                        'rdfs:label' => 'UnclassifiedAdultConsideration',
                        'schema:isPartOf' => [
                            '@id' => 'https://pending.schema.org'
                        ],
                        'schema:source' => [
                            '@id' => 'https://github.com/schemaorg/schemaorg/issues/2989'
                        ]
                    ],
                    [
                        '@id' => 'schema:hasOccupation',
                        '@type' => 'rdf:Property',
                        'rdfs:comment' => 'The Person\'s occupation. For past professions, use Role for expressing dates.',
                        'rdfs:label' => 'hasOccupation',
                        'schema:domainIncludes' => [
                            '@id' => 'schema:Person'
                        ],
                        'schema:rangeIncludes' => [
                            '@id' => 'schema:Occupation'
                        ],
                        'schema:source' => [
                            '@id' => 'https://github.com/schemaorg/schemaorg/issues/1698'
                        ]
                    ],
                    [
                        '@id' => 'schema:position',
                        '@type' => 'rdf:Property',
                        'rdfs:comment' => 'The position of an item in a series or sequence of items.',
                        'rdfs:label' => 'position',
                        'schema:domainIncludes' => [
                            [
                                '@id' => 'schema:ListItem'
                            ],
                            [
                                '@id' => 'schema:CreativeWork'
                            ]
                        ],
                        'schema:rangeIncludes' => [
                            [
                                '@id' => 'schema:Integer'
                            ],
                            [
                                '@id' => 'schema:Text'
                            ]
                        ]
                    ],
                    [
                        '@id' => 'schema:ComputerLanguage',
                        '@type' => 'rdfs:Class',
                        'rdfs:comment' => 'This type covers computer programming languages such as Scheme and Lisp, as well as other language-like computer representations. Natural languages are best represented with the [[Language]] type.',
                        'rdfs:label' => 'ComputerLanguage',
                        'rdfs:subClassOf' => [
                            '@id' => 'schema:Intangible'
                        ]
                    ],
                ],
                new SchemaOrgGraph(
                    new SchemaOrgClasses(
                        new SchemaOrgClass(
                            'BroadcastChannel',
                            'A unique instance of a BroadcastService on a CableOrSatelliteService lineup.',
                            'BroadcastChannel',
                            [
                                'Intangible'
                            ]
                        ),
                        new SchemaOrgClass(
                            'ComputerLanguage',
                            'This type covers computer programming languages such as Scheme and Lisp, as well as other language-like computer representations. Natural languages are best represented with the [[Language]] type.',
                            'ComputerLanguage',
                            [
                                'Intangible'
                            ]
                        )
                    ),
                    new SchemaOrgProperties(
                        new SchemaOrgProperty(
                            'musicGroupMember',
                            'A member of a music group&#x2014;for example, John, Paul, George, or Ringo.',
                            'musicGroupMember',
                            [
                                'MusicGroup'
                            ],
                            [
                                'Person'
                            ]
                        ),
                        new SchemaOrgProperty(
                            'hasOccupation',
                            'The Person\'s occupation. For past professions, use Role for expressing dates.',
                            'hasOccupation',
                            [
                                'Person'
                            ],
                            [
                                'Occupation'
                            ]
                        ),
                        new SchemaOrgProperty(
                            'position',
                            'The position of an item in a series or sequence of items.',
                            'position',
                            [
                                'ListItem',
                                'CreativeWork'
                            ],
                            [
                                'Integer',
                                'Text'
                            ]
                        )
                    )
                )
            ]
        ];
    }

    /**
     * @dataProvider propertiesForClassNameProvider
     */
    public function testGetPropertiesForClassName(SchemaOrgGraph $subject, string $className, GroupedSchemaOrgProperties $expectedProperties): void
    {
        Assert::assertEquals($expectedProperties, $subject->getPropertiesForClassName($className));
    }

    /**
     * @return iterable<string,<array<int,mixed>>
     */
    public function propertiesForClassNameProvider(): iterable
    {
        $thingClass = new SchemaOrgClass(
            'Thing',
            'The most generic type of item.',
            'Thing',
            []
        );
        $productClass = new SchemaOrgClass(
            'Product',
            'Any offered product or service. For example: a pair of shoes; a concert ticket; the rental of a car; a haircut; or an episode of a TV show streamed online.',
            'Product',
            [
                'Thing'
            ]
        );
        $nameProperty = new SchemaOrgProperty(
            'name',
            'The name of the item.',
            'name',
            [
                'Thing'
            ],
            [
                'Text'
            ]
        );
        $mpnProperty = new SchemaOrgProperty(
            'mpn',
            'The Manufacturer Part Number (MPN) of the product, or the product to which the offer refers.',
            'mpn',
            [
                'Demand',
                'Offer',
                'Product'
            ],
            [
                'Text'
            ]
        );

        $graph = new SchemaOrgGraph(
            new SchemaOrgClasses(
                $thingClass,
                $productClass
            ),
            new SchemaOrgProperties(
                $nameProperty,
                $mpnProperty
            )
        );

        yield [
            'subject' => $graph,
            'className' => 'Thing',
            new GroupedSchemaOrgProperties([
                'Thing' => new SchemaOrgProperties($nameProperty)
            ])
        ];

        yield [
            'subject' => $graph,
            'className' => 'Product',
            new GroupedSchemaOrgProperties([
                'Product' => new SchemaOrgProperties($mpnProperty),
                'Thing' => new SchemaOrgProperties($nameProperty)
            ])
        ];
    }
}
