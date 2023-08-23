<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\ContentRepository\Core\NodeType\NodeTypeManager;
use Neos\Flow\Annotations as Flow;

#[Flow\Scope("singleton")]
class SuperTypeResolver
{
    #[Flow\Inject]
    protected NodeTypeManager $nodeTypeManager;

    /**
     * @return array<int,string>
     */
    public function resolveSuperTypeCandidatesForPropertyName(SchemaOrgProperty $property): array
    {
        $supertypeCandidates = [];
        foreach ($this->nodeTypeManager->getNodeTypes() as $nodeType) {
            if (array_key_exists($property->id, $nodeType->getProperties())) {
                $supertypeCandidates[] = $nodeType->getName();
            }
        }

        return $supertypeCandidates;
    }
}
