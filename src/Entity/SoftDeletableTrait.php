<?php

namespace NS\AdminBundle\Entity;

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Trait SoftDeletableTrait
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true, hardDelete=false)
 */
trait SoftDeletableTrait
{
    use SoftDeleteableEntity;
}
