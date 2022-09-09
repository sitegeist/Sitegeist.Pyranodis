<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Neos\Utility\Arrays;
use Sitegeist\Noderobis\Domain\Generator\NodeTypeGenerator;
use Sitegeist\Noderobis\Domain\Specification\NodeTypeNameSpecification;
use Sitegeist\Noderobis\Domain\Specification\NodeTypeNameSpecificationCollection;
use Sitegeist\Noderobis\Domain\Specification\NodeTypeSpecification;
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
class NodeTypeCommandController extends CommandController
{
    #[Flow\Inject]
    protected SuperTypeResolver $superTypeResolver;

    #[Flow\Inject]
    protected NodeTypeGenerator $nodeTypeGenerator;

    public function kickstartFromSchemaOrgCommand(string $className, ?string $packageKey = null, ?string $prefix = null): void
    {
        $wizard = new SchemaSelectionWizard($this->output);
        $propertyTypeResolver = new PropertyTypeResolver();
        $graph = SchemaOrgGraph::createFromRemoteResource();

        $determineFlowPackageWizard = new DetermineFlowPackageWizard($this->output);
        $package = $determineFlowPackageWizard->determineFlowPackage($packageKey);

        $availableProperties = $graph->getPropertiesForClassName($className);
        $selectedProperties = $wizard->askForProperties($className, $availableProperties);

        $superTypeSpecifications = [];
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

            $supertypeCandidates = $this->superTypeResolver->resolveSuperTypeCandidatesForPropertyName($property);
            if (!empty($supertypeCandidates)) {
                $selectedSuperType = $wizard->askForSupertypesByProperty($property->id, $supertypeCandidates);
                if ($selectedSuperType) {
                    $superTypeSpecifications[] = NodeTypeNameSpecification::fromString($supertypeCandidates[$selectedSuperType]);
                } else {
                    $propertySpecifications[] = new PropertySpecification(
                        new PropertyNameSpecification($property->id),
                        $propertyTypeResolver->resolvePropertyType($property, $wizard),
                        new PropertyLabelSpecification($property->id),
                        new PropertyDescriptionSpecification($property->comment),
                    );
                }
            }
        }

        if (is_null($prefix)) {
            $prefix = $wizard->askForPrefix();
        }

        $nodeTypeSpecification = new NodeTypeSpecification(
            new NodeTypeNameSpecification($package->getPackageKey(), $prefix . '.' . $className),
            new NodeTypeNameSpecificationCollection(...$superTypeSpecifications),
            new PropertySpecificationCollection(...$propertySpecifications),
            new TetheredNodeSpecificationCollection(),
            false
        );

        $specificationRefinementWizard = new SpecificationRefinementWizard($this->output);
        $nodeTypeSpecification = $specificationRefinementWizard->refineSpecification($nodeTypeSpecification);

        $nodeType = $this->nodeTypeGenerator->generateNodeType($nodeTypeSpecification);

        $generateCodeWizard = new GenerateCodeWizard($this->output);
        $generateCodeWizard->generateCode($nodeType, $package);
    }
}
