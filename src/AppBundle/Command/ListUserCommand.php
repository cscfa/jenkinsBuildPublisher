<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\ApiUser;
use AppBundle\Entity\Repository\ApiUserRepository;
use Symfony\Component\Console\Helper\Table;

class ListUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("app:list:user");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(array(
            'username',
            'api key',
            'roles'
        ));

        foreach ($this->getUsers() as $user) {
            $table->addRow($this->getUserRow($user));
        }

        $table->render();
        return;
    }

    private function getUserRow(ApiUser $user)
    {
        return array(
            $user->getUsername(),
            $user->getApiKey(),
            implode(" ", $user->getRoles())
        );
    }

    private function getUsers()
    {
        return $this->getUserRepository()->findAll();
    }

    /**
     * @return ApiUserRepository
     */
    private function getUserRepository()
    {
        return $this->getContainer()->get('doctrine')->getManager()->getRepository(ApiUser::class);
    }
}
