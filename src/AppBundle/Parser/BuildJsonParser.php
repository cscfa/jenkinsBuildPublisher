<?php

namespace AppBundle\Parser;

use AppBundle\Parser\JsonParser;
use AppBundle\Entity\Build;
use Symfony\Component\Routing\RouterInterface;

class BuildJsonParser implements JsonParser
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

    private function toStd(Build $build)
    {
        $std = new \stdClass();
        $links = new \stdClass();
        $attributes = new \stdClass();

        $std->type = 'build';
        $std->id = $build->getId();
        $attributes->name = $build->getName();
        $attributes->project = $build->getProject()->getName();
        $attributes->status = $build->getStatus()->getName();

        $links->self = $this->router->generate('getBuild', array('id'=>$build->getId()));

        $projectLink = new \stdClass();
        $projectLink->href = $this->router->generate('getProject', array('id'=>$build->getProject()->getId()));
        $projectLinkMeta = new \stdClass();
        $projectLinkMeta->type = 'project';
        $projectLink->meta = $projectLinkMeta;
        $links->parent = $projectLink;

        $projectStatus = new \stdClass();
        $projectStatus->href = $this->router->generate('getStatus', array('id'=>$build->getStatus()->getId()));
        $projectStatusMeta = new \stdClass();
        $projectStatusMeta->type = 'status';
        $projectStatus->meta = $projectStatusMeta;
        $links->status = $projectStatus;

        $attributes->files = array();
        foreach ($build->getFiles() as $file) {
            $fileStd = new \stdClass();
            $fileStd->type = 'build_file';
            $fileStd->id = $file->getId();
            $fileStd->name = $file->getName();
            array_push($attributes->files, $fileStd);

            if (!isset($links->files)) {
                $links->files = array();
            }

            $fileLink = new \stdClass();
            $fileLink->href = $this->router->generate('getBuildFile', array('id'=>$file->getId()));
            $fileLinkMeta = new \stdClass();
            $fileLinkMeta->type = 'build_file';
            $fileLinkMeta->content_type = $file->getContentType();
            $fileLink->meta = $fileLinkMeta;

            array_push($links->files, $fileLink);
        }

        $std->attributes = $attributes;
        $std->links = $links;

        return $std;
    }
}
