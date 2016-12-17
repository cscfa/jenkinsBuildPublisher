<?php

namespace AppBundle\Misc\ProjectFinder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use AppBundle\Misc\Project\Project;

/**
 * Project finder
 *
 * This class is used to store the projects insformations
 *
 * @category ProjectFinder
 * @package JenkinsBuildPublisher
 * @author matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link http://cscfa.fr
 */
class ProjectFinder
{

    /**
     * Projects
     *
     * The finder projects
     *
     * @var array
     */
    private $projects = array();

    /**
     * Construct
     *
     * The default constructor
     *
     * @param string $resourceDir The resource directory where find the projects
     *
     * @return void
     */
    public function __construct($resourceDir)
    {
        $finder = new Finder();

        $projects = $finder->in($resourceDir)->directories()->depth(0);
        foreach ($projects as $project) {
            if ($project instanceof SplFileInfo) {
                $name = $project->getBasename();
                $this->projects[$name] = new Project($name, $project->getPathname());
            }
        }
    }

    /**
     * Get projects
     *
     * Return the finder projects
     *
     * @return array
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Get project
     *
     * Return the project according to given name
     *
     * @param string $project The project name
     *
     * @return Project
     */
    public function getProject($project)
    {
        return $this->projects[$project];
    }

    /**
     * Has project
     *
     * Test that the current finder has a project
     *
     * @param string $project The project name
     *
     * @return boolean
     */
    public function hasProject($project)
    {
        return isset($this->projects[$project]);
    }
}
