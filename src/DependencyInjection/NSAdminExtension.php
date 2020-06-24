<?php


namespace NS\AdminBundle\DependencyInjection;

use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class NSAdminExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ns_admin', $config);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'filters' => [
                    'softdeleteable' => [
                        'class'   => SoftDeleteableFilter::class,
                        'enabled' => true
                    ]
                ]
            ]
        ]);

        $container->prependExtensionConfig('stof_doctrine_extensions', [
            'orm' => [
                'default' => [
                    'softdeleteable' => true
                ],
            ]
        ]);
    }
}
