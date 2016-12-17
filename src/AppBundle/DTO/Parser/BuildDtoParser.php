<?php

namespace AppBundle\DTO\Parser;

use AppBundle\DTO\Parser\AbstractDtoParser;
use AppBundle\DTO\Parser\DtoParserInterface;
use Doctrine\ORM\EntityRepository;
use AppBundle\DTO\BuildDto;
use AppBundle\Entity\Build;

class BuildDtoParser extends AbstractDtoParser implements DtoParserInterface
{
    /**
     * @var EntityRepository
     */
    private $buildRepository;

    public function __construct(EntityRepository $repository)
    {
        $this->buildRepository = $repository;
    }

    protected function getDtoClass()
    {
        return BuildDto::class;
    }

    public function toEntity($dto)
    {
        $entity = $this->getEntity($dto);

        return $this->hydrateEntity($dto, $entity, array('id'));
    }

    public function toDto($entity)
    {
        return $this->hydrateDto(new BuildDto(), $entity);
    }

    private function getEntity(BuildDto $dto)
    {
        if ($dto->id !== null) {
            $entity = $this->buildRepository->find($dto->id);

            if ($entity !== null) {
                return $entity;
            }
        }

        return new Build();
    }
}
