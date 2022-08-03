<?php

/*
 * This file is part of the Sitegeist.Pyranodis package.
 */

declare(strict_types=1);

namespace Sitegeist\Pyranodis\Domain;

use Neos\Flow\Annotations as Flow;

#[Flow\Proxy(false)]
enum SchemaOrgType: string
{
    case TYPE_PROPERTY = 'rdf:Property';
    case TYPE_CLASS = 'rdfs:Class';
}
