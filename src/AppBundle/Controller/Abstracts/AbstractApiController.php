<?php

namespace AppBundle\Controller\Abstracts;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\DTO\Parser\DtoParserInterface;
use AppBundle\Parser\JsonParser;

abstract class AbstractApiController extends Controller
{
    /**
     * Manager
     *
     * The application entity manager
     *
     * @var EntityManager
     */
    private $manager;

    /**
     * Repository
     *
     * A set of entity repositories loaded by the
     * getRepository method. Indexed by entity class.
     *
     * @var array
     */
    private $repository = array();

    /**
     * Get dto parser
     *
     * Return a dto parser, accordingly with
     * the given type.
     *
     * @param string $type The dto type
     *
     * @return DtoParserInterface
     */
    protected function getDtoParser($type)
    {
        return $this->get('dto_parser-'.$type);
    }

    /**
     * Get json parser
     *
     * Return a json parser, accordingly with the
     * given type.
     *
     * @param string $type The parser type
     *
     * @return JsonParser
     */
    protected function getJsonParser($type)
    {
        return $this->get('json_parser-'.$type);
    }

    /**
     * Get repository
     *
     * Return a repository, according with the
     * given entity class
     * .
     * @return EntityRepository
     */
    protected function getRepository($entityClass)
    {
        if ($this->repository[$entityClass] !== null) {
            return $this->repository[$entityClass];
        }

        $this->repository[$entityClass] = $this->getManager()->getRepository($entityClass);
        return $this->repository[$entityClass];
    }

    /**
     * Get manager
     *
     * Return the current application entity
     * manager.
     *
     * @return EntityManager
     */
    protected function getManager()
    {
        if ($this->manager !== null) {
            return $this->manager;
        }

        $this->manager = $this->get('doctrine.orm.entity_manager');
        return $this->manager;
    }

    /**
     * Get error parser
     *
     * Return the application error parser
     *
     * @return ErrorParser
     */
    protected function getErrorParser()
    {
        return $this->get('error_parser');
    }

    /**
     * Get form parser
     *
     * Return the current application form parser.
     *
     * @return FormParser
     */
    protected function getFormParser()
    {
        return $this->get('form_parser');
    }
}
