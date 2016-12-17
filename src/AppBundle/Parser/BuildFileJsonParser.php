<?php

namespace AppBundle\Parser;

use AppBundle\Parser\JsonParser;
use Symfony\Component\Routing\RouterInterface;
use AppBundle\Entity\BuildFile;

class BuildFileJsonParser implements JsonParser
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

    private function toStd(BuildFile $file)
    {
        $std = new \stdClass();
        $links = new \stdClass();
        $attributes = new \stdClass();

        $std->type = 'build_file';
        $std->id = $file->getId();
        $attributes->name = $file->getName();
        $attributes->content_type = $file->getContentType();
        $attributes->content = $file->getContent();
        $attributes->build = $file->getBuild()->getName();

        $links->self = $this->router->generate('getBuildFile', array('id'=>$file->getId()));

        $buildLink = new \stdClass();
        $buildLink->href = $this->router->generate('getBuild', array('id'=>$file->getBuild()->getId()));
        $buildLinkMeta = new \stdClass();
        $buildLinkMeta->type = 'build';
        $buildLink->meta = $buildLinkMeta;
        $links->parent = $buildLink;

        $std->attributes = $attributes;
        $std->links = $links;

        return $std;
    }
}
