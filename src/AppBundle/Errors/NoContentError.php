<?php

namespace AppBundle\Errors;

use AppBundle\Errors\ApiErrorInterface;

class NoContentError implements ApiErrorInterface
{
    private $links;

    private $source;

    private $meta;

    private $details;

    public function __construct($formName, $pointer, $parameters = null, array $links = array(), array $meta = array())
    {
        $this->details = sprintf('The form \'%s\' has no content', $formName);

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
        return 'The form does not exist';
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
        return 'No content';
    }
    public function getCode()
    {
        return 204;
    }
}
