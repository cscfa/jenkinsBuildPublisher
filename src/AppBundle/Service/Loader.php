<?php

namespace AppBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Misc\ProjectFinder\ProjectFinder;
use AppBundle\Misc\Project\Project as MiscProject;
use AppBundle\Entity\Project as ProjectEntity;
use AppBundle\Entity\Build as BuildEntity;
use AppBundle\Misc\Build\EmbeddedBuild;
use AppBundle\Entity\Repository\ProjectRepository;

class Loader
{

    /**
     * @var ObjectManager
     */
    private $manager;

    private $resourceDir;

    public function __construct(ObjectManager $manager, $resourceDir)
    {
        $this->manager = $manager;
        $this->resourceDir = realpath($resourceDir);
    }

    public function load()
    {
        $finder = new ProjectFinder($this->resourceDir);

        $projects = $this->getProjects();
        $localProjects = $finder->getProjects();

        foreach ($projects as $project) {
            if (isset($localProjects[$project->getName()])) {
                $this->loadProject(
                    $localProjects[$project->getName()],
                    $project
                );
            }
        }

        foreach ($localProjects as $project) {
            if (isset($projects[$project->getName()])) {
                $this->loadProject(
                    $project,
                    $projects[$project->getName()]
                );
            }
        }

        return $finder;
    }

    private function loadProject(MiscProject $project, ProjectEntity $projectEntity = null)
    {
        if ($projectEntity !== null) {
            $builds = $projectEntity->getBuilds();

            foreach ($builds as $build) {
                $this->parseBuild($project, $build);
            }
        }
    }

    private function parseBuild(MiscProject $project, BuildEntity $build)
    {
        $buildName = $build->getName();

        $miscBuild = $project->getBuild($buildName);

        $path = null;
        if ($miscBuild !== null) {
            $path = $miscBuild->getPath();
        }
        $embedded = new EmbeddedBuild($buildName, $path, $build);

        if ($build->getStatus() !== null) {
            $embedded->setStatus($build->getStatus()->getName());
        }

        $project->replaceBuild($buildName, $embedded);
    }

    private function getProjects()
    {
        return $this->getRepository()->getByProjectName();
    }

    /**
     * @return ProjectRepository
     */
    private function getRepository()
    {
        return $this->manager->getRepository(ProjectEntity::class);
    }

}
