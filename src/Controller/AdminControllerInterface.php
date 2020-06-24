<?php


namespace NS\AdminBundle\Controller;


use Knp\Component\Pager\PaginatorInterface;
use NS\AdminBundle\Exception\AdminActionFailedException;
use NS\AdminBundle\Service\AdminServiceInterface;
use NS\FlashBundle\Service\Messages;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

interface AdminControllerInterface
{
    /**
     * @required
     *
     * @param PaginatorInterface $paginator
     * @param Messages           $flash
     */
    public function configure(PaginatorInterface $paginator, Messages $flash): void;

    /**
     * @return Response
     */
    public function indexAction(): Response;

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request): Response;

    /**
     * @param Request $request
     *
     * @param int     $id
     *
     * @return Response
     */
    public function deleteAction(Request $request, int $id): Response;

    /**
     * @param Request $request
     * @param         $id
     *
     * @return Response
     */
    public function viewAction(Request $request, $id): Response;

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request, $id): Response;

    /**
     * @param $id
     *
     * @return RedirectResponse
     */
    public function getEditSuccessRedirect($id): RedirectResponse;

    /**
     * @param $id
     *
     * @return RedirectResponse
     */
    public function getCreateSuccessRedirect($id): RedirectResponse;

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response;
}
