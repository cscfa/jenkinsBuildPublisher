<?php

namespace AppBundle\Parser;

use AppBundle\Parser\JsonParser;
use AppBundle\Entity\Project;
use AppBundle\Entity\Build;
use Symfony\Component\Routing\RouterInterface;

class ProjectJsonParser implements JsonParser
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

    private function toStd(Project $project)
    {
        $std = new \stdClass();
        $links = new \stdClass();
        $attributes = new \stdClass();

        $std->type = 'project';
        $std->id = $project->getId();
        $attributes->name = $project->getName();
        $links->self = $this->router->generate('getProject', array('id'=>$project->getId()));

        $builds = array();
        foreach ($project->getBuilds() as $build) {
            $buildId = $this->getBuildId($build);
            array_push(
                $builds,
                $buildId
            );

            $link = new \stdClass();
            $link->href = $this->router->generate('getBuild', array('id'=>$buildId));
            $link->meta = new \stdClass();
            $link->meta->type = 'build';

            if (!isset($links->builds)) {
                $links->builds = array();
            }
            array_push($links->builds, $link);
        }

        $attributes->builds = $builds;
        $std->attributes = $attributes;
        $std->links = $links;

        return $std;
    }

    private function getBuildId(Build $build)
    {
        return $build->getId();
    }
}
