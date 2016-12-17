<?php

namespace AppBundle\Parser;

use AppBundle\Parser\JsonParser;
use Symfony\Component\Routing\RouterInterface;
use AppBundle\Entity\Status;

class StatusJsonParser implements JsonParser
{
    /**
     * @var RouterInterface
     */
    private  $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function parseArray(array $array)
    {
        $stdArray = array();

        foreach ($array as $item) {
            array_push($stdArray, $this->toStd($item));
        }

        return json_encode($stdArray);
    }

    public function parse($item)
    {
        return json_encode($this->toStd($item));
    }

    private function toStd(Status $status)
    {
        $std = new \stdClass();
        $links = new \stdClass();
        $attributes = new \stdClass();

        $std->type = 'status';
        $std->id = $status->getId();
        $attributes->name = $status->getName();
        $links->self = $this->router->generate('getStatus', array('id'=>$status->getId()));

        $std->attributes = $attributes;
        $std->links = $links;

        return $std;
    }
}
