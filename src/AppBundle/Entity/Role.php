<?php

namespace AppBundle\Entity;

class Role
{

    private $id;

    private $label;


    /**
     * Set label
     *
     * @param string $label
     *
     * @return Role
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get id
     *
     * @return guid
     */
    public function getId()
    {
        return $this->id;
    }
}
