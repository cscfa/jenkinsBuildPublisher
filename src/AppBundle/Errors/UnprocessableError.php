<?php

namespace AppBundle\Errors;

use AppBundle\Errors\ApiErrorInterface;

class UnprocessableError implements ApiErrorInterface
{
    private $details;

    private $source;

    public function __construct($details, $pointer, $parameters) {
        $this->details = $details;

        $source = new \stdClass();
        $source->pointer = $pointer;
        $source->parameters = $parameters;

        $this->source = $source;
    }

    /**
     * {@inheritDoc}
     * @see \AppBundle\Errors\ApiErrorInterface::getLinks()
     */
    public function getLinks()
    {
        return array();
    }

    /**
     * {@inheritDoc}
     * @see \AppBundle\Errors\ApiErrorInterface::getTitle()
     */
    public function getTitle()
    {
        return 'The given entity cannot be processed due to invalid data';
    }

    /**
     * {@inheritDoc}
     * @see \AppBundle\Errors\ApiErrorInterface::getSource()
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritDoc}
     * @see \AppBundle\Errors\ApiErrorInterface::getMeta()
     */
    public function getMeta()
    {
        return array();
    }

    /**
     * {@inheritDoc}
     * @see \AppBundle\Errors\ApiErrorInterface::getDetails()
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * {@inheritDoc}
     * @see \AppBundle\Errors\ApiErrorInterface::getStatus()
     */
    public function getStatus()
    {
        return 'Unprocessable entity';
    }

    /**
     * {@inheritDoc}
     * @see \AppBundle\Errors\ApiErrorInterface::getCode()
     */
    public function getCode()
    {
        return 422;
    }
}