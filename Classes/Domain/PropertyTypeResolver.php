<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;
use Sitegeist\Noderobis\Domain\Specification\PropertyTypeSpecification;

#[Flow\Proxy(false)]
class PropertyTypeResolver
{
    public function resolvePropertyType(
        SchemaOrgProperty $property,
        SchemaSelectionWizard $wizard
    ): PropertyTypeSpecification {
        $typeSuggestions = $property->getTypeSuggestions();
        if (count($typeSuggestions) === 1) {
            return new PropertyTypeSpecification($typeSuggestions[0]);
        } else {
            return new PropertyTypeSpecification($typeSuggestions[$wizard->askForPropertyType($property)]);
        }
    }
}
