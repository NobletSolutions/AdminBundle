<?php


namespace NS\AdminBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use NS\AdminBundle\Entity\BaseAdminEntity;
use NS\AdminBundle\Event\AdminEvent;
use NS\AdminBundle\Exception\EntityNotFoundException;
use NS\AdminBundle\Repository\AbstractAdminManagedRepository;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

abstract class AdminService implements AdminServiceInterface
{
    /**
     * @var AbstractAdminManagedRepository
     */
    protected $repository;

    /**
     * @var EntityManagerInterface
     */
    protected $entity_manager;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    protected $class;
    /**
     * AdminService constructor.
     *
     * @param AbstractAdminManagedRepository $repository
     * @param EntityManagerInterface         $entity_manager
     * @param EventDispatcherInterface       $dispatcher
     */
    public function __construct(EntityManagerInterface $entity_manager, EventDispatcherInterface $dispatcher)
    {
        $this->entity_manager = $entity_manager;
        $this->dispatcher = $dispatcher;

        $this->repository = $this->entity_manager->getRepository($this->getClass());
    }

    /**
     * @return string
     */
    abstract public function getClass(): string;

    /**
     * @param null $order_by
     *
     * @return Query
     */
    public function getListQuery($order_by = null): Query
    {
        return $this->repository->getListQuery($order_by);
    }

    /**
     * @param null     $limit
     * @param string[] $sort
     *
     * @return array
     */
    public function findAll($limit = null, $sort = ['createdAt' => 'desc']): array
    {
        return $this->repository->findBy([], $sort, $limit);
    }

    /**
     * @param int $id
     *
     * @return BaseAdminEntity|null
     */
    public function find(int $id): ?object
    {
        return $this->repository->find($id);
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        $this->_delete($id);
    }

    /**
     * @param BaseAdminEntity $entity
     */
    public function edit(BaseAdminEntity $entity): void
    {
        $this->_edit($entity);
    }

    /**
     * @param BaseAdminEntity $entity
     */
    public function create(BaseAdminEntity $entity): void
    {
        $this->_create($entity);
    }

    protected function _delete(int $id): void
    {
        /**
         * @var BaseAdminEntity $entity
         */
        $entity = $this->repository->find($id);

        if(!$entity)
        {
            throw new EntityNotFoundException('Could not find '.$this->repository->getClassName().' for ID "'.$id.'"');
        }

        $this->entity_manager->remove($entity);
        $this->entity_manager->flush();

        $this->dispatcher->dispatch(new AdminEvent($entity), AdminEvent::DELETE);
    }

    /**
     * @param BaseAdminEntity $entity
     */
    protected function _edit(BaseAdminEntity $entity): void
    {
        $this->entity_manager->persist($entity);
        $this->entity_manager->flush();

        $this->dispatcher->dispatch(new AdminEvent($entity), AdminEvent::CREATE);
    }

    /**
     * @param BaseAdminEntity $entity
     */
    protected function _create(BaseAdminEntity $entity): void
    {
        $this->entity_manager->persist($entity);
        $this->entity_manager->flush();

        $this->dispatcher->dispatch(new AdminEvent($entity), AdminEvent::UPDATE);
    }
}
