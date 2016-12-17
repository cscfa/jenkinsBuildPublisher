<?php

namespace AppBundle\DTO\Parser;

interface DtoParserInterface
{
    public function toDto($entity);

    public function toEntity($dto);
}
