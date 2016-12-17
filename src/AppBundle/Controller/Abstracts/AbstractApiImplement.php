<?php

namespace AppBundle\Controller\Abstracts;

use AppBundle\Controller\Abstracts\AbstractApiController;
use AppBundle\DTO\Parser\DtoParserInterface;
use AppBundle\Parser\JsonParser;
use Doctrine\ORM\EntityRepository;

abstract class AbstractApiImplement extends AbstractApiController
{

    /**
     * Dto parser
     *
     * The current type dto parser
     *
     * @var DtoParserInterface
     */
    protected $dtoParser;

    /**
     * Json parser
     *
     * The current type json parser
     *
     * @var JsonParser
     */
    protected $jsonParser;

    /**
     * Repository
     *
     * The current entity repository
     *
     * @var EntityRepository
     */
    protected $repo;

    /**
     * Construct
     *
     * The default constructorù
     */
    public function __construct()
    {
        parent::__construct();

        $this->dtoParser = $this->getDtoParser($this->getType());

        $this->jsonParser = $this->getJsonParser($this->getType());

        $this->repo = $this->getRepository($this->getEntityClass());
    }

    abstract protected function getType();

    abstract protected function getEntityClass();

}
