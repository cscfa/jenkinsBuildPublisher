<?php

namespace AppBundle\DTO\Parser;

use AppBundle\DTO\Parser\AbstractDtoParser;
use AppBundle\DTO\Parser\DtoParserInterface;
use Doctrine\ORM\EntityRepository;
use AppBundle\DTO\BuildFileDto;
use AppBundle\Entity\BuildFile;

class BuildFileDtoParser extends AbstractDtoParser implements DtoParserInterface
{
    /**
     * @var EntityRepository
     */
    private $buildFileRepository;

    public function __construct(EntityRepository $repository)
    {
        $this->buildFileRepository = $repository;
    }

    protected function getDtoClass()
    {
        return BuildFileDto::class;
    }
    public function toEntity($dto)
    {
        $entity = $this->getEntity($dto);

        return $this->hydrateEntity($dto, $entity, array('id'));
    }
    public function toDto($entity)
    {
        return $this->hydrateDto(new BuildFileDto(), $entity);
    }

    private function getEntity(BuildFileDto $dto)
    {
        if ($dto->id !== null) {
            $entity = $this->buildFileRepository->find($dto->id);

            if ($entity !== null) {
                return $entity;
            }
        }

        return new BuildFile();
    }
}