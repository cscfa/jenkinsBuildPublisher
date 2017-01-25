<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use AppBundle\Misc\ProjectFinder\ProjectFinder;
use AppBundle\Entity\Project;
use AppBundle\Entity\Repository\ProjectRepository;
use AppBundle\Entity\Build;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\BuildFile;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Helper\ProgressBar;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Repository\BuildRepository;
use Symfony\Component\HttpFoundation\File\File;

class LocalToDBCommand extends ContainerAwareCommand
{
    private $projects = null;

    protected function configure()
    {
        $this->setName("app:db:update")
            ->addOption('dump', 'd', InputOption::VALUE_NONE, 'Dump the changements')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Execute the changements');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dump = $input->getOption('dump');
        $force = $input->getOption('force');

        $actions = $this->listProjectsChanges();

        if ($dump) {
            $this->dumpActions($actions, $output);

            if ($force) {

                $helper = $this->getHelper('question');
                $question = new ConfirmationQuestion(
                    sprintf('Continue (y/n) : '),
                    false
                );

                if (!$helper->ask($input, $output, $question)) {
                    $output->writeln('<error>ABORT</error>');
                    return 2;
                }
            }
        }

        if ($force) {
            $this->executeActions($actions, $output);
        }
    }

    private function persistAndFlush($entity)
    {
        $this->getDoctrineManager()->persist($entity);
        $this->getDoctrineManager()->flush();
    }

    private function addProject(\stdClass $project)
    {
        $projectEntity = new Project();
        $projectEntity->setName($project->name);

        $this->persistAndFlush($projectEntity);
    }

    private function addBuild(\stdClass $build)
    {
        $buildEntity = new Build();
        $buildEntity->setName($build->name);
        $buildEntity->setProject($this->getProject($build->project));

        $this->persistAndFlush($buildEntity);
    }

    private function addFile(\stdClass $file)
    {
        $buildEntity = new BuildFile();
        $buildEntity->setName($file->name);
        $buildEntity->setBuild(
            $this->getBuildRepository()
                ->findOneBy(
                    array(
                        'name'=>$file->build,
                        'project'=>$this->getProject($file->project)
                    )
                )
        );
        $buildEntity->setContentType($file->mimeType);
        $buildEntity->setContent($file->body);

        $this->persistAndFlush($buildEntity);
    }

    private function executeActions(\stdClass $actions, OutputInterface $output)
    {
        $progressBar = new ProgressBar($output, $actions->count);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %type%');

        $progressBar->setMessage('(project)', 'type');
        $progressBar->start();
        foreach ($actions->projects as $project) {
            $this->addProject($project);
            $progressBar->advance();
        }

        $progressBar->setMessage('(build)', 'type');
        foreach ($actions->builds as $build) {
            $this->addBuild($build);
            $progressBar->advance();
        }

        $progressBar->setMessage('(file)', 'type');
        foreach ($actions->files as $file) {
            $this->addFile($file);
            $progressBar->advance();
        }

        $progressBar->setMessage("\n", 'type');
        $progressBar->finish();
    }

    private function dumpActions(\stdClass $actions, OutputInterface $output)
    {
        $projectTable = new Table($output);
        $buildTable = new Table($output);
        $fileTable = new Table($output);

        $projectTable->setHeaders(array('name'));

        $buildTable->setHeaders(array('project', 'name'));

        $fileTable->setHeaders(array('project', 'build', 'name', 'content length'));

        foreach ($actions->projects as $project) {
            $projectTable->addRow(array($project));
        }

        foreach ($actions->builds as $build) {
            $buildTable->addRow(
                array(
                    $build->project,
                    $build->name
                )
            );
        }

        foreach ($actions->files as $file) {
            $fileTable->addRow(
                array(
                    $file->project,
                    $file->build,
                    $file->name,
                    $file->contentLength
                )
            );
        }

        foreach (
            array('project'=>$projectTable, 'build'=>$buildTable, 'file'=>$fileTable)
            as $name => $table
        ) {
            $output->writeln(sprintf('<comment>%s to create :</comment>', ucfirst($name)));
            $table->render();
        }
    }

    private function listProjectsChanges()
    {
        $actionCount = 0;
        $projects = array();
        $builds = array();
        $files = array();

        foreach ($this->getProjects() as $project) {
            if (!$this->isDBProject($project->getName())) {
                array_push($projects, $project->getName());
                $actionCount++;
            }

            foreach ($project->getBuilds() as $build) {
                $buildName = $build->getName();
                $projectName = $project->getName();
                $projectEntity = $this->getProject($projectName);

                if ($projectEntity === null || !$this->isDBBuild($projectEntity, $buildName)) {
                    $std = new \stdClass();
                    $std->project = $projectName;
                    $std->name = $build->getName();

                    array_push($builds, $std);
                    $actionCount++;
                }

                foreach ($build->getFiles() as $file) {
                    $buildEntity = null;
                    if ($projectEntity !== null) {
                        $projectBuilds = $projectEntity->getBuilds()->filter(function (Build $entity) use ($buildName) {
                            return $entity->getName() === $buildName;
                        });

                        $buildEntity = $projectBuilds->first();
                    }

                    if (
                        $projectEntity === null ||
                        !($buildEntity instanceof Build) ||
                        !$this->isDBFile($buildEntity, $file->getBasename())
                    ) {
                        if ($file instanceof SplFileInfo) {
                            $fileInstance = new File($file->getPathname());

                            $fileElement = new \stdClass();
                            $fileElement->project = $project->getName();
                            $fileElement->build = $build->getName();
                            $fileElement->path = $fileInstance->getPathname();
                            $fileElement->name = $fileInstance->getBasename();
                            $fileElement->contentLength = $fileInstance->getSize();
                            $fileElement->body = $file->getContents();
                            $fileElement->mimeType = $fileInstance->getMimeType();

                            array_push($files, $fileElement);
                            $actionCount++;
                        }
                    }
                }
            }
        }

        $result = new \stdClass();
        $result->projects = $projects;
        $result->builds = $builds;
        $result->files = $files;
        $result->count = $actionCount;

        return $result;
    }

    private function isDBFile(Build $build, $fileName)
    {
        return !($build->getFiles()->filter(function(BuildFile $entity) use ($fileName) {
            return $entity->getName() === $fileName;
        })->isEmpty());
    }

    private function isDBBuild(Project $project, $buildName)
    {
        return !($project->getBuilds()->filter(function(Build $entity) use ($buildName) {
            return $entity->getName() === $buildName;
        })->isEmpty());
    }

    private function isDBProject($name)
    {
        return $this->getProject($name) !== null;
    }

    private function getProjects()
    {
        return $this->getProjectFinder()->getProjects();
    }

    /**
     * @return Project
     */
    private function getProject($name)
    {
        if ($this->projects === null) {
            $this->projects = $this->getProjectRepository()->getByProjectName();
        }

        if (isset($this->projects[$name])) {
            return $this->projects[$name];
        }

        return null;
    }

    /**
     * @return ProjectRepository
     */
    private function getProjectRepository()
    {
        return $this->getContainer()->get('doctrine')->getManager()->getRepository(Project::class);
    }

    /**
     * @return BuildRepository
     */
    private function getBuildRepository()
    {
        return $this->getContainer()->get('doctrine')->getManager()->getRepository(Build::class);
    }

    /**
     * @return ProjectFinder
     */
    private function getProjectFinder()
    {
        return $this->getContainer()->get('project_loader')->load();
    }

    /**
     * @return EntityManager
     */
    private function getDoctrineManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
