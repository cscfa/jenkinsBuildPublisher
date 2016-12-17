<?php

namespace AppBundle\Entity;

class BuildFile
{

    private $id;

    private $name;

    private $contentType;

    private $content;

    private $build;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return BuildFile
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
     * Set contentType
     *
     * @param string $contentType
     *
     * @return BuildFile
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get contentType
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return BuildFile
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
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
     * Set build
     *
     * @param \AppBundle\Entity\Build $build
     *
     * @return BuildFile
     */
    public function setBuild(Build $build = null)
    {
        $this->build = $build;

        return $this;
    }

    /**
     * Get build
     *
     * @return \AppBundle\Entity\Build
     */
    public function getBuild()
    {
        return $this->build;
    }
}
