<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use AppBundle\Entity\Role;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRolesData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $defaultRoles = array(
            'ROLE_API'
        );

        $roleRepository = $manager->getRepository(Role::class);

        foreach ($defaultRoles as $roleLabel) {

            if ($roleRepository->findOneByLabel($roleLabel) !== null) {
                continue;
            }

            $role = new Role();

            $role->setLabel($roleLabel);

            $manager->persist($role);
            $manager->flush();
        }
    }
}