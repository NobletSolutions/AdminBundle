<?php


namespace NS\AdminBundle\Entity;


use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use \Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\MappedSuperclass()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true, hardDelete=false)
 */
class AdminSoftDeletableEntity extends BaseAdminEntity implements TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeleteableEntity;

    public function __construct()
    {
        $this->createdAt = $this->updatedAt = new \DateTime();
    }
}
