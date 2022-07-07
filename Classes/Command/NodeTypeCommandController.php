<?php

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
            JSON_PRETTY_PRINT
        );

        $entitySchema = null;
        $propertySchemas = [];

        foreach ($schemas['@graph'] as $schema) {
            if (
                $schema['@type'] === 'rdf:Property'
                && in_array(
                    ['@id' => 'schema:' . $entityName],
                    $schema['schema:domainIncludes'] ?? []
                )) {

                $propertySchemas[] = $schema;
            }
            if ($schema['@id'] === 'schema:' . $entityName) {
                $entitySchema = $schema;
            }
        }

        if (is_null($entitySchema)) {
            throw new \InvalidArgumentException('Could not resolve entity ' . $entityName, 1657190607);
        }

        if (is_null($selectedProperties)) {
            $propertyOptions = [];
            foreach ($propertySchemas as $id => $propertySchema) {
                $propertyOptions[] = '[' . $id . '] ' . $propertySchema['rdfs:label'] . ' - ' . \mb_substr($propertySchema['rdfs:comment'], 0, 150);
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
