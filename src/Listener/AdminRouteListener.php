<?php


namespace NS\AdminBundle\Listener;


use Symfony\Component\HttpKernel\Event\KernelEvent;

class AdminRouteListener
{
    public function onKernelRequest(KernelEvent $event)
    {
        $request = $event->getRequest();

        $model = $request->attributes->get('_admin_model', false);

        if($model)
        {
            $request->attributes->set('_controller', sprintf("App\Controller\Admin\%sController::%sAction",ucfirst($model).'Admin', $request->attributes->get('_action')));
        }
    }

}
