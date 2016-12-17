<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Misc\Project\Project;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $projects = array();
        foreach ($this->getProjectFinder()->getProjects() as $project) {
            if ($project instanceof Project) {
                $projects[] = $project;
            }
        }

        return $this->render('AppBundle:index:1.0.0.html.twig', ['projects' => $projects]);
    }

    /**
     * @Route("/project/{projectName}", name="projectpage")
     */
    public function projectAction($projectName)
    {
        $finder = $this->getProjectFinder();

        if (!$finder->hasProject($projectName)) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $project = $finder->getProject($projectName);
        $builds = $project->getBuilds();

        return $this->render(
            'AppBundle:project:1.0.0.html.twig',
            [
                'project' => $project,
                'builds' => array_reverse($builds)
            ]
        );
    }

    /**
     * @Route("/build/{projectName}/{buildName}", name="buildpage")
     */
    public function buildAction($projectName, $buildName)
    {
        $finder = $this->getProjectFinder();

        if (!$finder->hasProject($projectName)) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $project = $finder->getProject($projectName);

        if (!$project->hasBuild($buildName)) {
            return $this->redirect($this->generateUrl('projectpage', array('projectName' => $projectName)));
        }
        $build = $project->getBuild($buildName);

        return $this->render(
            'AppBundle:build:1.0.0.html.twig',
            [
                'project' => $project,
                'build' => $build
            ]
        );
    }

    private function getProjectFinder()
    {
        return $this->get('project_loader')->load();
    }
}
