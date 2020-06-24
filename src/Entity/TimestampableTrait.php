<?php

namespace NS\AdminBundle\Entity;

/**
 * Trait TimestampableTrait
 *
 * @package NS\AdminBundle\Entity
 */
trait TimestampableTrait
{
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $value
     */
    public function setCreatedAt(\DateTime $value = null)
    {
        $this->createdAt = $value ? clone $value : new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $value
     */
    public function setUpdatedAt(\DateTime $value = null)
    {
        $this->updatedAt = $value ? clone $value : new \DateTime();
    }
}
