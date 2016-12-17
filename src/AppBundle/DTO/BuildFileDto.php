<?php

namespace AppBundle\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;

class BuildFileDto
{
    public $id;

    /**
     * @NotBlank()
     */
    public $name;

    /**
     * @NotBlank()
     */
    public $contentType;

    /**
     * @NotBlank()
     */
    public $content;

    /**
     * @NotBlank()
     */
    public $build;
}
