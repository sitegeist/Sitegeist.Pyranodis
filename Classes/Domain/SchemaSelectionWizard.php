<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\ConsoleOutput;

#[Flow\Proxy(false)]
class SchemaSelectionWizard
{
    public function __construct(
        private readonly ConsoleOutput $output
    ) {
    }

    public function askForProperties(string $entityName, SchemaOrgProperties $availableProperties): string
    {
        $propertyOptions = [];
        foreach ($availableProperties as $i => $property) {
            $propertyOptions[] =
                '[' . $i . ' | ' . $property->id . '] '
                . \mb_substr(\str_replace("\n", ' ', $property->comment), 0, 150);
        }
        return $this->output->ask(
            array_merge(
                [
                    'Which properties of ' . $entityName . ' do you want to use?'
                ],
                $propertyOptions,
                [
                    ''
                ]
            ),
        );
    }

    public function askForPropertyType(SchemaOrgProperty $property): int
    {
        $propertyTypeOptions = [];
        foreach ($property->getTypeSuggestions() as $i => $propertyType) {
            $propertyTypeOptions[] = '[' . $i . '] ' . $propertyType;
        }
        return (int)$this->output->ask(
            array_merge(
                [
                    'Which type do you want to use for property ' . $property->id . '?'
                ],
                $propertyTypeOptions,
                [
                    ''
                ]
            ),
        );
    }

    public function askForPrefix(): string
    {
        return $this->output->ask(
            array_merge(
                [
                    'Which prefix do you want to use (e.g. Document,Content,Mixin)?'
                ]
            ),
        );
    }

    /**
     * @param array<int,string> $superTypeCandidates
     */
    public function askForSupertypesByProperty(string $propertyName, array $superTypeCandidates): int
    {
        $superTypeOptions = [];
        foreach ($superTypeCandidates as $i => $superTypeName) {
            $superTypeOptions[] = '[' . $i . '] ' . $superTypeName;
        }
        return (int)$this->output->ask(
            array_merge(
                [
                    'There are eligible supertypes that also declare property ' . $propertyName . ', which one do you want to use?'
                ],
                $superTypeOptions,
                [
                    ''
                ]
            ),
        );
    }
}
