<?php

namespace AppBundle\DTO\Parser;

use AppBundle\DTO\Parser\DtoParserInterface;
use AppBundle\DTO\ProjectDto;
use AppBundle\Entity\Repository\ProjectRepository;
use AppBundle\Entity\Project;

class ProjectDtoParser extends AbstractDtoParser implements DtoParserInterface
{
    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    public function __construct(ProjectRepository $repository)
    {
        $this->projectRepository = $repository;
    }

    protected function getDtoClass()
    {
        return ProjectDto::class;
    }

    /**
     * {@inheritDoc}
     * @return ProjectDto
     * @see \AppBundle\DTO\Parser\DtoParserInterface::toDto()
     */
    public function toDto($entity)
    {
        return $this->hydrateDto(new ProjectDto(), $entity);
    }

    /**
     * {@inheritDoc}
     * @return Project
     * @see \AppBundle\DTO\Parser\DtoParserInterface::toEntity()
     */
    public function toEntity($dto)
    {
        $entity = $this->getEntity($dto);

        return $this->hydrateEntity($dto, $entity, array('id'));
    }

    private function getEntity(ProjectDto $dto)
    {
        if ($dto->id !== null) {
            $entity = $this->projectRepository->find($dto->id);

            if ($entity !== null) {
                return $entity;
            }
        }

        return new Project();
    }
}