<?php

namespace AppBundle\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;

class BuildDto
{
    public $id;

    /**
     * @NotBlank()
     */
    public $name;

    /**
     * @NotBlank()
     */
    public $project;

    /**
     * @NotBlank()
     */
    public $status;

    public $files;
}
