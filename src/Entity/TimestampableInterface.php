<?php


namespace NS\AdminBundle\Entity;


interface TimestampableInterface
{
    public function getCreatedAt();
    public function setCreatedAt(\DateTime $value);
    public function getUpdatedAt();
    public function setUpdatedAt(\DateTime $value);
}

