<?php


namespace NS\AdminBundle\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;

abstract class AbstractAdminManagedRepository extends ServiceEntityRepository
{
    /**
     * @param string $order_by
     * @param bool   $order
     *
     * @return \Doctrine\ORM\Query
     */
    public function getListQuery($order_by = '', $order = false): Query
    {
        $q = $this->createQueryBuilder('e');

        if($order_by)
        {
            $q->orderBy($order_by, $order ?: 'ASC');
        }

        return $q->getQuery();
    }
}
