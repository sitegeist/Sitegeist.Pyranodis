<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Command;

use Neos\Flow\Cli\CommandController;
use Neos\Flow\Annotations as Flow;
use Neos\Utility\Files;

#[Flow\Scope("singleton")]
class NodeTypeCommandController extends CommandController
{
    /**
     * @param string $entityName
     * @param string|null $selectedProperties
     * @return void
     */
    public function kickstartWithSchemaOrgCommand(string $entityName, ?string $selectedProperties = null): void
    {
        $schemas = \json_decode(
            Files::getFileContents('https://schema.org/version/latest/schemaorg-current-https.jsonld'),
            true,
            JSON_THROW_ON_ERROR
        );

        $entitySchema = null;
        $propertySchemas = [];

        foreach ($schemas['@graph'] as $schema) {
            if (
                $schema['@type'] === 'rdf:Property'
                && (
                    in_array(
                        ['@id' => 'schema:' . $entityName],
                        $schema['schema:domainIncludes'] ?? []
                    )
                    || ($schema['schema:domainIncludes'] ?? []) === ['@id' => 'schema:' . $entityName]
                )
            ) {
                $propertySchemas[] = $schema;
            }
            if ($schema['@id'] === 'schema:' . $entityName) {
                $entitySchema = $schema;
            }
        }
        usort(
            $propertySchemas,
            function (array $schemaA, array $schemaB) {
                $labelA = is_array($schemaA['rdfs:label']) ? $schemaA['rdfs:label']['@value'] : $schemaA['rdfs:label'];
                $labelB = is_array($schemaB['rdfs:label']) ? $schemaB['rdfs:label']['@value'] : $schemaB['rdfs:label'];

                return $labelA <=> $labelB;
            }
        );

        if (is_null($entitySchema)) {
            throw new \InvalidArgumentException('Could not resolve entity ' . $entityName, 1657190607);
        }

        if (is_null($selectedProperties)) {
            $propertyOptions = [];
            foreach ($propertySchemas as $id => $propertySchema) {
                $label = is_array($propertySchema['rdfs:label']) ? $propertySchema['rdfs:label']['@value'] : $propertySchema['rdfs:label'];
                $comment = is_array($propertySchema['rdfs:comment']) ? $propertySchema['rdfs:comment']['@value'] : $propertySchema['rdfs:comment'];
                $propertyOptions[] = '[' . $id . ' | ' . $label . '] '  . \mb_substr(\str_replace("\n", ' ', $comment), 0, 150);
            }
            $selectedProperties = $this->output->ask(
                array_merge(
                    [
                        'Which properties of ' . $entityName . ' do you want to use?'
                    ],
                    $propertyOptions
                ),
            );
        }

        \Neos\Flow\var_dump($selectedProperties);
        exit();
    }
}
