<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Misc\ProjectFinder\ProjectFinder;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("app:test");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectFinder = new ProjectFinder(
            realpath(
                $this->getContainer()->getParameter('kernel.root_dir').'/../resources/projects'
            )
        );

        print_r($projectFinder);

    }
}
