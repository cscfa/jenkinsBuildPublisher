<?php

namespace AppBundle\Entity;

class Error
{
    private $id;

    private $date;

    private $message;

    private $code;

    private $user;

    private $meta;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->meta = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Error
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Error
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set code
     *
     * @param integer $code
     *
     * @return Error
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
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
     * Set user
     *
     * @param \AppBundle\Entity\ApiUser $user
     *
     * @return Error
     */
    public function setUser(\AppBundle\Entity\ApiUser $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\ApiUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add meta
     *
     * @param \AppBundle\Entity\ErrorMeta $meta
     *
     * @return Error
     */
    public function addMeta(\AppBundle\Entity\ErrorMeta $meta)
    {
        $this->meta[] = $meta;

        return $this;
    }

    /**
     * Remove meta
     *
     * @param \AppBundle\Entity\ErrorMeta $meta
     */
    public function removeMeta(\AppBundle\Entity\ErrorMeta $meta)
    {
        $this->meta->removeElement($meta);
    }

    /**
     * Get meta
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMeta()
    {
        return $this->meta;
    }
}
