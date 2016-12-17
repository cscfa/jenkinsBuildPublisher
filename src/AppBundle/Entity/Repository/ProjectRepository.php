<?php

namespace AppBundle\Entity\Repository;

/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjectRepository extends \Doctrine\ORM\EntityRepository
{

    public function getByProjectName()
    {
        return $this->_em->createQueryBuilder()
            ->select('s')
            ->from($this->_entityName, 's', 's.name')
            ->getQuery()
            ->getResult();
    }

}
