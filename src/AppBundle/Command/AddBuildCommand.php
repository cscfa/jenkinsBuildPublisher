<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Project;
use AppBundle\Entity\Status;
use AppBundle\Entity\Build;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddBuildCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("app:add:build")
            ->addArgument('name', InputArgument::REQUIRED, 'The build name')
            ->addArgument('project', InputArgument::REQUIRED, 'The build name')
            ->addArgument('status', InputArgument::REQUIRED, 'The build name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $buildName = $input->getArgument('name');
        $projectName = $input->getArgument('project');
        $statusName = $input->getArgument('status');

        $manager = $this->getManager();

        $project = $this->getProject($manager, $projectName);
        $status = $this->getStatus($manager, $statusName);

        if ($project === null) {
            $output->writeln('<error>Project \''.$projectName.'\' not found</error>');
            return;
        } else if ($status === null) {
            $output->writeln('<error>Status \''.$statusName.'\' not found</error>');
            return;
        }

        $buildRepository = $manager->getRepository(Build::class);

        if ($buildRepository->findOneByName($buildName) !== null) {
            $output->writeln('<error>The build \''.$buildName.'\' already exist</error>');
            return;
        }

        $build = new Build();
        $build->setName($buildName)
            ->setProject($project)
            ->setStatus($status);

        $manager->persist($build);
        $manager->flush();
    }

    private function getStatus(ObjectManager $manager, $status)
    {
        $statusRepository = $manager->getRepository(Status::class);

        return $statusRepository->findOneByName($status);
    }

    private function getProject(ObjectManager $manager, $project)
    {
        $projectRepository = $manager->getRepository(Project::class);

        return $projectRepository->findOneByName($project);
    }

    /**
     * @return ObjectManager
     */
    private function getManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
