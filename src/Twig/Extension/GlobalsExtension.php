<?php


namespace NS\AdminBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class GlobalsExtension extends AbstractExtension implements GlobalsInterface
{
    protected $config;

    /**
     * GlobalsExtension constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getGlobals(): array
    {
        return [
            'ns_admin' => [
                'base_template' => $this->config['base_template']
            ]
        ];
    }
}
