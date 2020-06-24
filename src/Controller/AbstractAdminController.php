<?php


namespace NS\AdminBundle\Controller;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Knp\Component\Pager\PaginatorInterface;
use NS\AdminBundle\Entity\BaseAdminEntity;
use NS\AdminBundle\Exception\AdminActionFailedException;
use NS\AdminBundle\Exception\EntityNotFoundException;
use NS\AdminBundle\Service\AdminService;
use NS\AdminBundle\Service\AdminServiceInterface;
use NS\FlashBundle\Service\Messages;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract class AbstractAdminController extends AbstractController implements AdminControllerInterface
{
    /**
     * @var EntityManager
     */
    protected $entity_manager;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var Messages
     */
    protected $flash;

    /**
     * @var AdminService
     */
    protected $admin_service;

    /**
     * @return string
     */
    abstract protected function getEditFormClass(): string;

    /**
     * AbstractAdminController constructor.
     *
     * @param AdminServiceInterface $admin_service
     */
    public function __construct(AdminServiceInterface $admin_service)
    {
        $this->admin_service = $admin_service;
    }

    /**
     * @param BaseAdminEntity $entity
     *
     * @return FormInterface
     */
    protected function getEditForm(BaseAdminEntity $entity): FormInterface
    {
        return $this->createForm($this->getEditFormClass(), $entity);
    }

    protected function getListQuery(): Query
    {
        return $this->admin_service->getListQuery();
    }

    protected function getListTemplate(): string
    {
        return '@NSAdmin/list.html.twig';
    }

    protected function getEditTemplate(): string
    {
        return '@NSAdmin/edit.html.twig';
    }

    protected function getViewTemplate(): string
    {
        return '@NSAdmin/view.html.twig';
    }

    protected function getCreateTemplate(): string
    {
        return '@NSAdmin/create.html.twig';
    }

    /**
     * @param BaseAdminEntity $entity
     */
    protected function edit(BaseAdminEntity $entity): void
    {
        $this->admin_service->edit($entity);
    }

    /**
     * @param BaseAdminEntity $entity
     */
    protected function create(BaseAdminEntity $entity): void
    {
        $this->admin_service->create($entity);
    }

    /**
     * @param int $id
     *
     * @throws EntityNotFoundException
     */
    protected function delete(int $id): void
    {
        $this->admin_service->delete($id);
    }

    /**
     * @param int $id
     *
     * @return BaseAdminEntity|null
     */
    protected function getRecord(int $id): ?BaseAdminEntity
    {
        return $this->admin_service->find($id);
    }

    /**
     * @param BaseAdminEntity $entity
     *
     * @return FormInterface
     */
    protected function getCreateForm(BaseAdminEntity $entity): FormInterface
    {
        return $this->getEditForm($entity);
    }

    protected function getNewEntity(): BaseAdminEntity
    {
        //this will be problematic if you have objects with constructor arguments...
        $class = $this->getClass();
        return new $class();
    }

    /**
     * @required
     *
     * @param PaginatorInterface $paginator
     * @param Messages           $flash
     */
    public function configure(PaginatorInterface $paginator, Messages $flash): void
    {
        $this->model     = strtolower((new \ReflectionClass($this->getClass()))->getShortName());
        $this->paginator = $paginator;
        $this->flash     = $flash;
    }

    /**
     * @return int
     */
    protected function getNumPerPage(): int
    {
        return 25;
    }

    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->redirect($this->generateUrl('admin_model_list', ['_admin_model' => $this->model]));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        $results = $this->getListQuery();

        $pagination = $this->paginator->paginate(
            $results,
            $request->query->getInt('page', 1),
            $this->getNumPerPage()
        );

        return $this->render($this->getListTemplate(), ['pagination' => $pagination, 'model' => $this->model]);
    }

    /**
     * @param $model
     */
    protected function setDeleteMessage($model): void
    {
        $this->flash->addSuccess(null, 'The ' . $model . ' has been deleted.');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request, int $id): Response
    {
        if(!$this->isAllowed('delete', $id))
        {
            throw new AccessDeniedException('You are not authorized to access this page.');
        }

        if(!$id)
        {
            throw new NotFoundHttpException('No record provided.');
        }

        try
        {
            $this->delete($id);
            $this->setDeleteMessage($this->model);
        }
        catch(AdminActionFailedException $e)
        {
            $this->flash->addError(null, $e->getMessage());
        }

        return $this->redirectToDefault();
    }

    public function viewAction(Request $request, $id): Response
    {
        if(!$this->isAllowed('view', $id))
        {
            throw new AccessDeniedException('You are not authorized to access this page.');
        }

        $object = $this->getRecord($id);

        if($object === null)
        {
            throw new NotFoundHttpException('Record does not exist.');
        }

        return $this->render($this->getViewTemplate(), ['object' => $object, 'model' => $this->model]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request, $id): Response
    {
        if(!$this->isAllowed('edit', $id))
        {
            throw new AccessDeniedException('You are not authorized to access this page.');
        }

        if(!$id)
        {
            throw new NotFoundHttpException('No record provided.');
        }

        $object = $this->getRecord($id);

        if($object === null)
        {
            throw new NotFoundHttpException('Record does not exist.');
        }

        $form = $this->getEditForm($object);
        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                try
                {
                    $this->edit($form->getData());
                    $this->flash->addSuccess(null, 'Your changes have been saved.');
                    return $this->getEditSuccessRedirect($object->getId());
                }
                catch(AdminActionFailedException $e)
                {
                    $this->flash->addError(null, $e->getMessage());
                }
            }
            else
            {
                $this->flash->addError(null, 'There was an error saving your changes');
            }
        }

        return $this->render($this->getEditTemplate(),
                             ['form' => $form->createView(), 'object' => $object, 'model' => $this->model]);
    }

    public function getEditSuccessRedirect($id): RedirectResponse
    {
        return $this->redirectToRoute('admin_model_list', ['_admin_model' => $this->model]);
    }

    public function getCreateSuccessRedirect($id): RedirectResponse
    {
        return $this->redirectToRoute('admin_model_list', ['_admin_model' => $this->model]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        if(!$this->isAllowed('create'))
        {
            throw new AccessDeniedException('You are not authorized to access this page.');
        }

        $object = $this->getNewEntity();

        $form = $this->getCreateForm($object);

        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            if($form->isValid())
            {
                try
                {
                    $this->create($form->getData());
                    $this->flash->addSuccess(null, ucfirst($this->model) . ' has been saved.');
                    return $this->getCreateSuccessRedirect($object->getId());
                }
                catch(AdminActionFailedException $e)
                {
                    $this->flash->addError($e->getMessage());
                }
            }
            else
            {
                $this->flash->addError(null, 'There was an error saving your changes');
            }
        }

        return $this->render($this->getCreateTemplate(),
                             ['form' => $form->createView(), 'object' => $object, 'model' => $this->model]);
    }

    /**
     * @return Response
     */
    protected function redirectToDefault(): Response
    {
        return $this->redirectToRoute('admin_model_index', ['_admin_model' => $this->model]);
    }

    /**
     * @param string   $action
     * @param int|null $id
     *
     * @return bool
     */
    protected function isAllowed(string $action, ?int $id = null): bool
    {
        return true;
    }

    /**
     * @return string
     */
    protected function getClass(): string
    {
        return $this->admin_service->getClass();
    }
}
