<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Command;

use Neos\ContentRepository\Domain\Service\NodeTypeManager;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Flow\Configuration\ConfigurationManager;
use Sitegeist\Noderobis\Domain\Generator\NodeTypeGenerator;
use Sitegeist\Noderobis\Domain\Specification\NodeTypeNameSpecification;
use Sitegeist\Noderobis\Domain\Specification\NodeTypeNameSpecificationCollection;
use Sitegeist\Noderobis\Domain\Specification\NodeTypeSpecification;
use Sitegeist\Noderobis\Domain\Specification\OptionsSpecification;
use Sitegeist\Noderobis\Domain\Specification\PropertyDescriptionSpecification;
use Sitegeist\Noderobis\Domain\Specification\PropertyLabelSpecification;
use Sitegeist\Noderobis\Domain\Specification\PropertyNameSpecification;
use Sitegeist\Noderobis\Domain\Specification\PropertySpecification;
use Sitegeist\Noderobis\Domain\Specification\PropertySpecificationCollection;
use Sitegeist\Noderobis\Domain\Specification\TetheredNodeSpecificationCollection;
use Sitegeist\Noderobis\Domain\Wizard\DetermineFlowPackageWizard;
use Sitegeist\Noderobis\Domain\Wizard\GenerateCodeWizard;
use Sitegeist\Noderobis\Domain\Wizard\SpecificationRefinementWizard;
use Sitegeist\Pyranodis\Domain\PropertyTypeResolver;
use Sitegeist\Pyranodis\Domain\SchemaOrgGraph;
use Sitegeist\Pyranodis\Domain\SchemaOrgProperty;
use Sitegeist\Pyranodis\Domain\SchemaSelectionWizard;
use Sitegeist\Pyranodis\Domain\SuperTypeResolver;

#[Flow\Scope("singleton")]
class KickstartCommandController extends CommandController
{
    public function __construct(
        private readonly SuperTypeResolver $superTypeResolver,
        private readonly NodeTypeGenerator $nodeTypeGenerator,
        private readonly NodeTypeManager $nodeTypeManager,
        private readonly ConfigurationManager $configurationManager
    ) {
        parent::__construct();
    }


    public function nodeTypeFromSchemaOrgCommand(
        string $className,
        ?string $packageKey = null,
        ?string $prefix = null
    ): void {
        $wizard = new SchemaSelectionWizard($this->output);
        $propertyTypeResolver = new PropertyTypeResolver();
        $graph = SchemaOrgGraph::createFromRemoteResource();

        $determineFlowPackageWizard = new DetermineFlowPackageWizard();
        $package = $determineFlowPackageWizard->determineFlowPackage($packageKey);

        $totalAvailableProperties = $graph->getPropertiesForClassName($className);
        $availableProperties = $totalAvailableProperties
            ->reduceToManageable(
                $this->configurationManager->getConfiguration(
                    'Settings',
                    'Neos.Neos.userInterface.inspector.dataTypes'
                ),
                $this->nodeTypeManager->getNodeTypes(false)
            );
        $selectedProperties = $wizard->askForProperties(
            $className,
            $availableProperties,
            implode(', ', $totalAvailableProperties->getDifference($availableProperties)->getAllProperties())
        );
        if (in_array('-e', $selectedProperties)) {
            $availableProperties = $totalAvailableProperties;
            $selectedProperties = $wizard->askForProperties(
                $className,
                $availableProperties,
                null
            );
        }

        $superTypeSpecifications = [];
        $propertySpecifications = [];
        foreach ($selectedProperties as $selectedProperty) {
            $property = $availableProperties->getById($selectedProperty);
            if (!$property instanceof SchemaOrgProperty) {
                throw new \InvalidArgumentException('Unknown property ' . $selectedProperty, 1660050534);
            }

            $supertypeCandidates = $this->superTypeResolver->resolveSuperTypeCandidatesForPropertyName($property);
            if (!empty($supertypeCandidates)) {
                $selectedSuperTypeName = $wizard->askForSupertypesByProperty($property->id, $supertypeCandidates);
                if ($selectedSuperTypeName !== SchemaSelectionWizard::SELECTED_SUPERTYPE_NONE) {
                    $superTypeSpecifications[] = NodeTypeNameSpecification::fromString($selectedSuperTypeName);
                    continue;
                }
            }

            $propertySpecifications[] = new PropertySpecification(
                new PropertyNameSpecification($property->id),
                $propertyTypeResolver->resolvePropertyType($property, $wizard),
                null,
                new PropertyLabelSpecification($property->id),
                new PropertyDescriptionSpecification($property->comment),
            );
        }

        if (is_null($prefix)) {
            $prefix = $wizard->askForPrefix();
        }

        $nodeTypeSpecification = new NodeTypeSpecification(
            new NodeTypeNameSpecification($package->getPackageKey(), $prefix . '.' . $className),
            new NodeTypeNameSpecificationCollection(...$superTypeSpecifications),
            new PropertySpecificationCollection(...$propertySpecifications),
            new TetheredNodeSpecificationCollection(),
            false,
            null,
            null,
            new OptionsSpecification([
                'schemaOrgClass' => 'https://schema.org/' . $className
            ])
        );

        $specificationRefinementWizard = new SpecificationRefinementWizard($this->output);
        $nodeTypeSpecification = $specificationRefinementWizard->refineSpecification($nodeTypeSpecification);

        $nodeType = $this->nodeTypeGenerator->generateNodeType($nodeTypeSpecification);

        $generateCodeWizard = new GenerateCodeWizard($this->output);
        $generateCodeWizard->generateCode($nodeType, $package);
    }
}
