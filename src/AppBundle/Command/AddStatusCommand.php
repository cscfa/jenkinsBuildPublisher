<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Status;

class AddStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("app:add:status")
            ->addArgument('name', InputArgument::REQUIRED, 'The status name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $statusName = $input->getArgument('name');

        $manager = $this->getManager();
        $statusRepository = $manager->getRepository(Status::class);

        if ($statusRepository->findOneByName($statusName) !== null) {
            $output->writeln('<error>The status \''.$statusName.'\' already exist</error>');
            return;
        }

        $status = new Status();
        $status->setName($statusName);

        $manager->persist($status);
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
