<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Tests\Unit\Domain;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Sitegeist\Pyranodis\Domain\SchemaOrgClass;
use Sitegeist\Pyranodis\Domain\SchemaOrgClasses;
use Sitegeist\Pyranodis\Domain\SchemaOrgGraph;
use Sitegeist\Pyranodis\Domain\SchemaOrgProperties;
use Sitegeist\Pyranodis\Domain\SchemaOrgProperty;

class SchemaOrgGraphTest extends TestCase
{
    /**
     * @dataProvider graphProvider
     * @param array<string,mixed> $sample
     */
    public function testFromSchemaOrgJsonArray(array $sample, SchemaOrgGraph $expectedClass): void
    {
        Assert::assertEquals($expectedClass, SchemaOrgGraph::fromSchemaOrgJsonArray($sample));
    }

    /**
     * @return array<int,array<int,array<string,mixed>|SchemaOrgGraph>>
     */
    public function graphProvider(): array
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
                            ]
                        ),
                        new SchemaOrgProperty(
                            'hasOccupation',
                            'The Person\'s occupation. For past professions, use Role for expressing dates.',
                            'hasOccupation',
                            [
                                'Person'
                            ]
                        ),
                        new SchemaOrgProperty(
                            'position',
                            'The position of an item in a series or sequence of items.',
                            'position',
                            [
                                'ListItem',
                                'CreativeWork'
                            ]
                        )
                    )
                )
            ]
        ];
    }

    /*
    public function testGetPropertiesForClassName(SchemaOrgGraph $subject, string $className, SchemaOrgProperties $expectedProperties): void
    {
        Assert::assertEquals($expectedProperties, $subject->getPropertiesForClassName($className));
    }*/
}
