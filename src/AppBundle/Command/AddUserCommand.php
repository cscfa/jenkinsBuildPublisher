<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\ApiUser;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Role;

class AddUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("app:add:user")
            ->addArgument('username', InputArgument::REQUIRED, 'The user name')
            ->addArgument('password', InputArgument::REQUIRED, 'The user password')
            ->addArgument('apikey', InputArgument::REQUIRED, 'The user api key')
            ->addOption('role', 'r', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'The user roles', array());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $apikey = $input->getArgument('apikey');

        $roles = $input->getOption('role');

        $manager = $this->getManager();
        $userRepository = $manager->getRepository(ApiUser::class);

        if ($userRepository->findOneByUsername($username) !== null) {
            $output->writeln('<error>The user \''.$username.'\' already exist</error>');
            return;
        } else if ($userRepository->findOneByApiKey($apikey) !== null) {
            $output->writeln('<error>The api key \''.$apikey.'\' already exist</error>');
            return;
        }

        if (!is_array($roles) && is_string($roles)) {
            $roles = array($roles);
        } else if (!is_array($roles)) {
            $roles = array();
        }

        $user = new ApiUser($username, $apikey, $password);

        $rolesEntities = array();
        foreach ($roles as $role) {
            $roleEntity = $this->getRole($role, $manager);

            if ($roleEntity instanceof Role) {
                array_push($rolesEntities, $roleEntity);
            } else {
                $output->writeln('<error>Role \''.$role.'\' not found</error>');
                return;
            }
        }

        $user->setRoles($rolesEntities);

        $manager->persist($user);
        $manager->flush();
    }

    /**
     * @param string $role
     * @param EntityManager $manager
     *
     * @return Role
     */
    private function getRole($role, EntityManager $manager)
    {
        $repo = $manager->getRepository(Role::class);

        return $repo->findOneByLabel($role);
    }

    /**
     * @return EntityManager
     */
    private function getManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
