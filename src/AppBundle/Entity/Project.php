<?php

namespace AppBundle\Entity;

class Project
{

    private $id;

    private $name;

    private $builds;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->builds = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add build
     *
     * @param Build $build
     *
     * @return Project
     */
    public function addBuild(Build $build)
    {
        $this->builds[] = $build;

        return $this;
    }

    /**
     * Remove build
     *
     * @param Build $build
     */
    public function removeBuild(Build $build)
    {
        $this->builds->removeElement($build);
    }

    /**
     * Get builds
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBuilds()
    {
        return $this->builds;
    }

    public function __toString()
    {
        return $this->id;
    }
}
