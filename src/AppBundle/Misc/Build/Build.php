<?php

namespace AppBundle\Misc\Build;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Build
 *
 * This class is used to store a build informations
 *
 * @category Build
 * @package JenkinsBuildPublisher
 * @author matthieu vallance <matthieu.vallance@cscfa.fr>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @link http://cscfa.fr
 */
class Build
{

    /**
     * Name
     *
     * The build name
     *
     * @var string
     */
    private $name;

    /**
     * Path
     *
     * The build path
     *
     * @var string
     */
    private $path;

    /**
     * Files
     *
     * The build files
     *
     * @var array[SplFileInfo]
     */
    private $files = array();

    /**
     * Construct
     *
     * The default constructor
     *
     * @param string $name The build name
     * @param string $path The build path
     *
     * @return void
     */
    public function __construct($name, $path)
    {
        $this->name = $name;
        $this->path = $path;

        if ($this->path !== null) {
            $this->initFiles();
        }
    }

    /**
     * Init files
     *
     * Initialize the build files
     *
     * @return void
     */
    private function initFiles()
    {
        $finder = new Finder();

        $files = $finder->in($this->path);

        foreach ($files as $file) {
            if ($file instanceof SplFileInfo) {
                $extension = $file->getExtension();
                $name = $file->getBasename('.'.$extension);
                $this->files[$name] = $file;
            }
        }
    }

    /**
     * Get name
     *
     * Return the build name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get file names
     *
     * Return the build files names
     *
     * @return array
     */
    public function getFileNames()
    {
        return array_keys($this->files);
    }

    /**
     * Get files
     *
     * Return the build files
     *
     * @return SplFileInfo[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Get file
     *
     * Return a file according with the given name
     *
     * @param string $fileName The file name
     *
     * @return SplFileInfo
     */
    public function getFile($fileName)
    {
        return $this->files[$fileName];
    }

    public function getFileContent($fileName)
    {
        $content = $this->getFile($fileName)->getContents();

        $lines = preg_split ('/$\R?^/m', $content);
        $maxCount = count($lines);
        $maxLength = strlen(((string)$maxCount));

        $resultLines = array();
        foreach ($lines as $index => $line) {
            $lineCount = sprintf('% '.$maxLength.'d', $index);

            $resultLines[] = '<span class="line_number">'.$lineCount.'.</span> '.str_replace(' ', '&nbsp;', htmlspecialchars($line));
        }

        return implode('<br/>', $resultLines);
    }

    /**
     * Get files count
     *
     * Return the build file count
     *
     * @return integer
     */
    public function getFilesCount()
    {
        return count($this->files);
    }
}
