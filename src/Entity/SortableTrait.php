<?php

namespace NS\AdminBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;

trait SortableTrait
{
    /**
     * @Gedmo\SortablePosition
     * @var integer
     */
    protected $position;

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}
