<?php

namespace AppBundle\Errors;

use AppBundle\Errors\ApiErrorInterface;

class NotFoundError implements ApiErrorInterface
{
    private $links;

    private $source;

    private $meta;

    private $details;

    public function __construct($entity, $id, $pointer, $parameters = null, array $links = array(), array $meta = array())
    {
        $this->details = sprintf('The entity %s with id %s cannot be found', $entity, $id);

        $source = new \stdClass();
        $source->pointer = $pointer;

        if ($parameters !== null) {
            $source->parameters = $parameters;
        }
        $this->source = $source;

        $this->links = $links;
        $this->meta = $meta;
    }

    public function getLinks()
    {
        return $this->links;
    }
    public function getTitle()
    {
        return 'Entity not found';
    }
    public function getSource()
    {
        return $this->source;
    }
    public function getMeta()
    {
        return $this->meta;
    }
    public function getDetails()
    {
        return $this->details;
    }
    public function getStatus()
    {
        return 'Not found';
    }
    public function getCode()
    {
        return 404;
    }
}