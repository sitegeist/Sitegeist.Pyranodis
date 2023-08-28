<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\ConsoleOutput;
use Symfony\Component\Console\Question\ChoiceQuestion;

#[Flow\Proxy(false)]
final readonly class SchemaSelectionWizard
{
    public const SELECTED_SUPERTYPE_NONE = 'none';

    public function __construct(
        private ConsoleOutput $output
    ) {
    }

    /**
     * @return array<string>
     */
    public function askForProperties(
        string $entityName,
        GroupedSchemaOrgProperties $availableProperties,
        ?string $extensionOptions
    ): array {
        $propertyOptions = [];
        $autoCompleterValues = [];
        $i = 0;
        foreach ($availableProperties as $groupName => $properties) {
            if ($properties->isEmpty()) {
                continue;
            }
            if ($i !== 0) {
                $propertyOptions[str_repeat(' ', 2 * $i)] = '';
            }
            $propertyOptions[str_repeat(' ', 2 * $i + 1)] = '     --- Properties from ' . $groupName . ' ---';
            foreach ($properties as $property) {
                $propertyOptions[$property->id]
                    = \mb_substr(\str_replace("\n", ' ', $property->comment), 0, 150);
                $autoCompleterValues[] = $property->id;
            }
            $i++;
        }

        if ($extensionOptions) {
            $propertyOptions['-e'] = 'Show additional properties that have been filtered out'
                . ' due to missing editor options (' . $extensionOptions . ')';
            $autoCompleterValues[] = '-e';
        }

        return $this->output->askQuestion(
            (new ChoiceQuestion(
                'Which properties of ' . $entityName . ' do you want to use?',
                $propertyOptions
            ))->setMultiselect(true)
            ->setAutocompleterValues($autoCompleterValues)
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
    public function askForSupertypesByProperty(string $propertyName, array $superTypeCandidates): string
    {
        return $this->output->askQuestion(
            (new ChoiceQuestion(
                'There are eligible supertypes that also declare property ' . $propertyName
                    . ', which one do you want to use?',
                array_merge(
                    [self::SELECTED_SUPERTYPE_NONE],
                    $superTypeCandidates
                ),
                null
            ))->setAutocompleterValues($superTypeCandidates)
        );
    }
}
