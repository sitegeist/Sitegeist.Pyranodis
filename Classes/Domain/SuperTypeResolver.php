<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\ContentRepository\Core\Factory\ContentRepositoryId;
use Neos\ContentRepositoryRegistry\ContentRepositoryRegistry;
use Neos\Flow\Annotations as Flow;

#[Flow\Scope("singleton")]
class SuperTypeResolver
{
    #[Flow\Inject]
    protected ContentRepositoryRegistry $contentRepositoryRegistry;

    /**
     * @return array<int,string>
     */
    public function resolveSuperTypeCandidatesForPropertyName(SchemaOrgProperty $property): array
    {
        $supertypeCandidates = [];
        foreach ($this->contentRepositoryRegistry->get(ContentRepositoryId::fromString('default'))->getNodeTypeManager()->getNodeTypes() as $nodeType) {
            if (array_key_exists($property->id, $nodeType->getProperties())) {
                $supertypeCandidates[] = $nodeType->name->value;
            }
        }

        return $supertypeCandidates;
    }
}
