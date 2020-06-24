<?php


namespace NS\AdminBundle\Event;


use NS\AdminBundle\Entity\BaseAdminEntity;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class DeletedEvent
 *
 * @package NS\AdminBundle\Event
 */
class AdminEvent extends Event
{
    public const CREATE = 'admin.entity.created',
        UPDATE = 'admin.entity.updated',
        DELETE = 'admin.entity.deleted';

    /**
     * @var BaseAdminEntity
     */
    protected $entity;

    /**
     * DeletedEvent constructor.
     *
     * @param BaseAdminEntity $entity
     */
    public function __construct(BaseAdminEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return BaseAdminEntity
     */
    public function getEntity(): BaseAdminEntity
    {
        return $this->entity;
    }

}
