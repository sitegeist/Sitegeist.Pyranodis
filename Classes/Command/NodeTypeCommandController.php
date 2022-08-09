<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Command;

use Neos\Flow\Cli\CommandController;
use Neos\Flow\Annotations as Flow;
use Sitegeist\Pyranodis\Domain\SchemaOrgGraph;

#[Flow\Scope("singleton")]
class NodeTypeCommandController extends CommandController
{
    /**
     * @param string $entityName
     * @param string|null $selectedProperties
     * @return void
     */
    public function kickstartFromSchemaOrgCommand(string $entityName, ?string $selectedProperties = null): void
    {
        $graph = SchemaOrgGraph::createFromRemoteResource();

        $availableProperties = $graph->getPropertiesForClassName($entityName);

        if (is_null($selectedProperties)) {
            $propertyOptions = [];
            foreach ($availableProperties as $i => $property) {
                $propertyOptions[] =
                    '[' . $i . ' | ' . $property->id . '] '
                    . \mb_substr(\str_replace("\n", ' ', $property->comment), 0, 150);
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
