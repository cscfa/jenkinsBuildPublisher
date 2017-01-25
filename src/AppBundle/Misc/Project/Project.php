<?php

namespace AppBundle\Misc\Project;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use AppBundle\Misc\Build\Build;

/**
 * Project
 *
 * This class is used to store a project insformation
 *
 * @category Project
 * @package JenkinsBuildPublisher
 * @author matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link http://cscfa.fr
 */
class Project
{

    /**
     * Name
     *
     * The project name
     *
     * @var string
     */
    private $name;

    /**
     * Path
     *
     * The project path
     *
     * @var string
     */
    private $path;

    /**
     * Builds
     *
     * The project builds
     *
     * @var array
     */
    private $builds = array();

    /**
     * Construct
     *
     * The default constructor
     *
     * @param string $name The project name
     * @param string $path The project path
     *
     * @return void
     */
    public function __construct($name, $path)
    {
        $this->name = $name;
        $this->path = $path;

        $this->initBuilds();
    }

    /**
     * Init builds
     *
     * Initialize the project builds
     *
     * @return void
     */
    private function initBuilds()
    {
        $finder = new Finder();
        $directories = $finder->in($this->path)
            ->directories()
            ->depth(0);

        $buildDirector = null;
        foreach ($directories as $directory) {
            if ($directory instanceof SplFileInfo) {
                if ($directory->getBasename() == 'builds') {
                    $buildDirector = $directory->getPathname();
                    break;
                }
            }
        }

        if ($buildDirector !== null) {
            $buildFinder = new Finder();

            $builds = $buildFinder->in($buildDirector)
                ->directories()
                ->depth(0);

            foreach ($builds as $build) {
                if ($build instanceof SplFileInfo) {
                    $this->builds[$build->getBasename()] = new Build($build->getBasename(), $build->getPathname());
                }
            }
        }
    }

    /**
     * Get name
     *
     * Return the project name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get build count
     *
     * Return the build count of the project
     *
     * @return integer
     */
    public function getBuildCount()
    {
        return count($this->builds);
    }

    /**
     * Get builds
     *
     * Return the project builds
     *
     * @return array
     */
    public function getBuilds()
    {
        return $this->builds;
    }

    /**
     * Has build
     *
     * Test that the current project has a build
     *
     * @param string $build The build name
     *
     * @return boolean
     */
    public function hasBuild($build)
    {
        return isset($this->builds[$build]);
    }

    /**
     * Get build
     *
     * Return a build, according with the given build name
     *
     * @param string $build The build name
     *
     * @return Build
     */
    public function getBuild($build)
    {
        if (isset($this->builds[$build])) {
            return $this->builds[$build];
        }

        return null;
    }

    /**
     * Replace build
     *
     * Replace a build with another
     *
     * @param string $buildName The build name to replace
     * @param Build  $build     The build to replace with
     *
     * @return void
     */
    public function replaceBuild($buildName, Build $build)
    {
        $this->builds[$buildName] = $build;
    }

}
