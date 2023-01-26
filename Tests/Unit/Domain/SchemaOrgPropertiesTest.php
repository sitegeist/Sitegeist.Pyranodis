<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Tests\Unit\Domain;

use Neos\ContentRepository\Domain\Model\NodeType;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Sitegeist\Pyranodis\Domain\SchemaOrgProperties;
use Sitegeist\Pyranodis\Domain\SchemaOrgProperty;

class SchemaOrgPropertiesTest extends TestCase
{
    /**
     * @dataProvider propertiesProvider
     */
    public function testReduceToManageable(
        SchemaOrgProperties $sampleProperties,
        array $declaredPropertyTypes,
        array $declaredNodeTypes,
        SchemaOrgProperties $expectedProperties
    ): void {
        Assert::assertEquals(
            $expectedProperties,
            $sampleProperties->reduceToManageable(
                $declaredPropertyTypes,
                $declaredNodeTypes
            )
        );
    }

    /**
     * @return iterable<string,array<mixed>>
     */
    public function propertiesProvider(): iterable
    {
        $abstract = new SchemaOrgProperty(
            'schema:abstract',
            'An abstract is a short description that summarizes a [[CreativeWork]].',
            'abstract',
            [
                'CreativeWork'
            ],
            [
                'Text'
            ]
        );
        $copyrightHolder = new SchemaOrgProperty(
            'schema:copyrightHolder',
            'The party holding the legal copyright to the CreativeWork.',
            'copyrightHolder',
            [
                'CreativeWork'
            ],
            [
                'Organization',
                'Person'
            ]
        );
        $timeRequired = new SchemaOrgProperty(
            'schema:timeRequired',
            'Approximate or typical time it takes to work with or through this learning resource for the typical intended target audience, e.g. \'PT30M\', \'PT1H25M\'.',
            'timeRequired',
            [
                'CreativeWork'
            ],
            [
                'Duration'
            ]
        );
        $keywords = new SchemaOrgProperty(
            'schema:keywords',
            'Keywords or tags used to describe some item. Multiple textual entries in a keywords list are typically delimited by commas, or by repeating the property.',
            'keywords',
            [
                'Place',
                'CreativeWork',
                'Organization',
                'Product',
                'Event'
            ],
            [
                'DefinedTerm',
                'Text',
                'URL'
            ]
        );

        yield 'baseTestCase' => [
            'sampleProperties' => new SchemaOrgProperties(
                $abstract,
                $copyrightHolder,
                $timeRequired,
                $keywords
            ),
            'declaredPropertyTypes' => [],
            'declaredNodeTypes' => [],
            new SchemaOrgProperties(
                $abstract,
                $keywords
            )
        ];

        yield 'valueObjectTestCase' => [
            'sampleProperties' => new SchemaOrgProperties(
                $abstract,
                $copyrightHolder,
                $timeRequired,
                $keywords
            ),
            'declaredPropertyTypes' => [
                'Acme\Site\Domain\Duration' => [
                    'editor' => 'Acme.Site/Inspector/Editor/DurationEditor'
                ]
            ],
            'declaredNodeTypes' => [],
            new SchemaOrgProperties(
                $abstract,
                $timeRequired,
                $keywords
            )
        ];

        yield 'matchingNodeTypeNameTestCase' => [
            'sampleProperties' => new SchemaOrgProperties(
                $abstract,
                $copyrightHolder,
                $timeRequired,
                $keywords
            ),
            'declaredPropertyTypes' => [],
            'declaredNodeTypes' => [
                new NodeType(
                    'Acme.Site:Document.Organization',
                    [],
                    []
                )
            ],
            new SchemaOrgProperties(
                $abstract,
                $copyrightHolder,
                $keywords
            )
        ];

        yield 'matchingNodeTypeOptionsTestCase' => [
            'sampleProperties' => new SchemaOrgProperties(
                $abstract,
                $copyrightHolder,
                $timeRequired,
                $keywords
            ),
            'declaredPropertyTypes' => [],
            'declaredNodeTypes' => [
                new NodeType(
                    'Acme.Site:Document.MySpecialOrganization',
                    [],
                    [
                        'options' => [
                            'schemaOrgClassName' => 'Organization'
                        ]
                    ]
                )
            ],
            new SchemaOrgProperties(
                $abstract,
                $copyrightHolder,
                $keywords
            )
        ];
    }
}
