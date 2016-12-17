<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Status;

class LoadStatusData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $defaultStatus = array(
            Status::SUCCESS,
            Status::FAILED,
            Status::UNSTABLE
        );

        $statusRepository = $manager->getRepository(Status::class);

        foreach ($defaultStatus as $statusName) {

            if ($statusRepository->findOneByName($statusName) !== null) {
                continue;
            }

            $status = new Status();

            $status->setName($statusName);

            $manager->persist($status);
            $manager->flush();
        }
    }
}