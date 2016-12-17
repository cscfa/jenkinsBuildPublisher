<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Project;

class AddProjectCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("app:add:project")
            ->addArgument('name', InputArgument::REQUIRED, 'The project name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectName = $input->getArgument('name');

        $manager = $this->getManager();
        $projectRepository = $manager->getRepository(Project::class);

        if ($projectRepository->findOneByName($projectName) !== null) {
            $output->writeln('<error>The project \''.$projectName.'\' already exist</error>');
            return;
        }

        $project = new Project();
        $project->setName($projectName);

        $manager->persist($project);
        $manager->flush();

        $output->writeln('<info>DONE</info>');
        return;
    }

    /**
     * @return ObjectManager
     */
    private function getManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
