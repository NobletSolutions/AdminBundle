<?php


namespace NS\AdminBundle\Service;


use Doctrine\ORM\Query;
use NS\AdminBundle\Entity\BaseAdminEntity;

interface AdminServiceInterface
{
    /**
     * @return string
     */
     function getClass(): string;

    /**
     * @param null $order_by
     *
     * @return Query
     */
     public function getListQuery($order_by = null): Query;

    /**
     * @param null     $limit
     * @param string[] $sort
     *
     * @return array
     */
     public function findAll($limit = null, $sort = ['createdAt' => 'desc']): array;

    /**
     * @param int $id
     *
     * @return BaseAdminEntity|null
     */
     public function find(int $id): ?object;

    /**
     * @param int $id
     */
     public function delete(int $id): void;

    /**
     * @param BaseAdminEntity $entity
     */
     public function edit(BaseAdminEntity $entity): BaseAdminEntity;

    /**
     * @param BaseAdminEntity $entity
     */
     public function create(BaseAdminEntity $entity): BaseAdminEntity;
}
