<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;
use Sitegeist\Noderobis\Domain\Specification\NodeTypeNameSpecification;
use Sitegeist\Noderobis\Domain\Specification\NodeTypeNameSpecificationCollection;
use Sitegeist\Noderobis\Domain\Specification\NodeTypeSpecification;
use Sitegeist\Noderobis\Domain\Specification\PropertyDescriptionSpecification;
use Sitegeist\Noderobis\Domain\Specification\PropertyLabelSpecification;
use Sitegeist\Noderobis\Domain\Specification\PropertyNameSpecification;
use Sitegeist\Noderobis\Domain\Specification\PropertySpecification;
use Sitegeist\Noderobis\Domain\Specification\PropertySpecificationCollection;
use Sitegeist\Noderobis\Domain\Specification\PropertyTypeSpecification;
use Sitegeist\Noderobis\Domain\Specification\TetheredNodeSpecificationCollection;
use Sitegeist\Pyranodis\Domain\SchemaOrgGraph;
use Sitegeist\Pyranodis\Domain\SchemaOrgProperties;
use Sitegeist\Pyranodis\Domain\SchemaOrgProperty;

#[Flow\Scope("singleton")]
class NodeTypeCommandController extends \Sitegeist\Noderobis\Command\AbstractCommandController
{
    public function kickstartFromSchemaOrgCommand(string $className, ?string $packageKey = null, ?string $prefix = null): void
    {
        $graph = SchemaOrgGraph::createFromRemoteResource();

        $package = $this->determinePackage($packageKey);
        $availableProperties = $graph->getPropertiesForClassName($className);
        $selectedProperties = $this->askForProperties($className, $availableProperties);

        $propertySpecifications = [];
        foreach (Arrays::trimExplode(',', $selectedProperties) as $selectedProperty) {
            if (is_numeric($selectedProperty)) {
                $property = $availableProperties->getByIndex((int)$selectedProperty);
            } else {
                $property = $availableProperties->getById($selectedProperty);
            }
            if (!$property instanceof SchemaOrgProperty) {
                throw new \InvalidArgumentException('Unknown property ' . $selectedProperty, 1660050534);
            }

            $typeSuggestions = $property->getTypeSuggestions();
            if (count($typeSuggestions) === 1) {
                $propertyType = $typeSuggestions[0];
            } else {
                $propertyType = $typeSuggestions[(int)$this->askForPropertyType($property)];
            }
            $propertySpecifications[] = new PropertySpecification(
                new PropertyNameSpecification($property->id),
                new PropertyTypeSpecification($propertyType),
                new PropertyLabelSpecification($property->id),
                new PropertyDescriptionSpecification($property->comment),
            );
        }

        if (is_null($prefix)) {
            $prefix = $this->askForPrefix();
        }

        $this->generateNodeTypeFromSpecification(
            new NodeTypeSpecification(
                new NodeTypeNameSpecification($package->getPackageKey(), $prefix . '.' . $className),
                new NodeTypeNameSpecificationCollection(),
                new PropertySpecificationCollection(...$propertySpecifications),
                new TetheredNodeSpecificationCollection(),
                false
            ),
            $package
        );
    }

    private function askForProperties(string $entityName, SchemaOrgProperties $availableProperties): string
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

    private function askForPropertyType(SchemaOrgProperty $property): string
    {
        $propertyTypeOptions = [];
        foreach ($property->getTypeSuggestions() as $i => $propertyType) {
            $propertyTypeOptions[] = '[' . $i . '] ' . $propertyType;
        }
        return $this->output->ask(
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

    private function askForPrefix(): string
    {
        return $this->output->ask(
            array_merge(
                [
                    'Which prefix do you want to use (e.g. Document,Content,Mixin)?'
                ]
            ),
        );
    }
}
