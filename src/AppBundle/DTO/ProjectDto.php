<?php

namespace AppBundle\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;

class ProjectDto
{
    public $id;

    /**
     * @NotBlank()
     */
    public $name;

    public $builds;
}
