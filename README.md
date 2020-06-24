# AdminBundle
Simple framework for generating SF4/5 admin.  Allows you to create simple admin pages for managing entities by creating a basic entity, service, repository, and controller class for each entity.

Requires more coding than using an admin generator like Sonata, but allows for much greater flexibility by ditching complex configurators for simple, easily-overridden and extended classes.

##Installation

Require the AdminBundle

`composer require ns/admin-bundle`

add it to your project bundles.php

`NS\AdminBundle\NSAdminBundle::class => ['all' => true],`

and require the routing config in your project routes.yaml file

```yaml
ns_admin:
    resource: "@NSAdminBundle/Resources/config/routing.yml"
    prefix:  /admin
```

Create your entity, and extend `AdminSoftDeletableEntity`. Remember to define a `__toString()` method:
```php
class FooBar extends NS\AdminBundle\Entity\AdminSoftDeletableEntity
{
    public function __toString()
    {
        return 'somestring';
    }
}
```
You can extend `BaseAdminEntity` instead, if you don't need soft delete functionality.

Create the entity repository, extend `AbstractAdminManagedRepository`, and define the constructor:
```php
class FooBarRepository extends NS\AdminBundle\Repository\AbstractAdminManagedRepository
{
    public function __construct(Doctrine\Persistence\ManagerRegistry $registry)
    {
        parent::__construct($registry, FooBar::class);
    }
}
```

Create the entity service, extend `AdminService`, and define the getClass() method:
```php
class FooBarService extends NS\AdminBundle\Service\AdminService
{
    public function getClass(): string
    {
        return FooBar::class;
    }
}
```

Create the edit form for your entity:
```php
class FooBarEditType extends Symfony\Component\Form\AbstractType
{
    public function buildForm()
    {
        //...
    }
}
```

Create the admin list template for your entity, extending `'@NSAdmin/list.html.twig'`. Define the following blocks:
    
* `model_create_icon`
* `list_column_headers`
* `list_row blocks`

If using a 'view' page instead of proceeding straight from the list to the edit form, override `view_button`, uncomment the button code, and modify as needed, then create the view template and extend `'@NSAdmin/view.html.twig'`

Create the admin controller for your entity, extending `AbstractAdminController` and define the following methods:
```php
class FoobarAdminController extends NS\AdminBundle\Controller\AbstractAdminController
{
    public function __construct(FooBarService $admin_service)
    {
        parent::__construct($admin_service);
    }
    
    protected function getEditFormClass(): string
    {
        return FooBarEditType::class;
    }
    
    protected function getListTemplate(): string
    {
        return 'path/to/list.html.twig';
    }

    //Only needed if using a view template:
    protected function getViewTemplate(): string
    {
        return 'path/to/view.html.twig';
    }

}
```
The built-in routing will automatically route requests to a controller matching the format **%Entityclass%AdminController**, where **%Entityclass%** is an initial-caps variant of your entity class name.

`{{ path('admin_model_index', {'_admin_model'=>'foobar'}) }}`

For our example of FooBar, and admin controller class of `FoobarAdminController` will be automatically routed to; `FooBarAdminController` will require manual routing.

To add an entry to the MenuBuilder in ColorAdminBundle, use the parameters `['route' => 'admin_model_index', 'routeParameters' => ['_admin_model' => 'foobar']]`
