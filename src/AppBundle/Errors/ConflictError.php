<?php

namespace AppBundle\Errors;

use AppBundle\Errors\ApiErrorInterface;

class ConflictError implements ApiErrorInterface
{
    private $links;

    private $source;

    private $meta;

    private $details;

    public function __construct($entity, $dataName, $data, $pointer, $parameters = null, array $links = array(), array $meta = array())
    {
        $this->details = sprintf('The entity %s with \'%s\' : \'%s\' create a conflict with the existent entities', $entity, $dataName, $data);

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
        return 'The given entity cannot be processed due to coonflicting data';
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
        return 'Conflict';
    }
    public function getCode()
    {
        return 409;
    }
}