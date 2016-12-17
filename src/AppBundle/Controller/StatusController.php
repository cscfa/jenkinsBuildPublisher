<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Status;
use AppBundle\Parser\StatusJsonParser;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Errors\NotFoundError;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Route("/api")
 */
class StatusController extends Controller
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * Get statuses
     *
     * This route is used to get all of the existing statuses.
     *
     * @Route("/statuses", name="getStatuses")
     * @Method({"GET"})
     * @ApiDoc(
     *  description="Get all status",
     *  section="status",
     *  headers={
     *      {
     *          "name"="apikey",
     *          "description"="the user api key",
     *          "required"=true
     *      }
     *  },
     *  statusCodes={
     *      200="Returned on success",
     *      403="Returned on apikey authentication error"
     *  }
     * )
     */
    public function getAllAction()
    {
        $statuses = $this->getRepository()->findAll();
        $parser = $this->getStatusParser();

        return new JsonResponse(
            $parser->parseArray((array)$statuses),
            200,
            array(),
            true
        );
    }

    /**
     * Get status
     *
     * This route is used to get a specified status resource.
     *
     * @Route("/status/{id}", name="getStatus")
     * @Method({"GET"})
     * @ApiDoc(
     *  description="Get a status",
     *  section="status",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="guid",
     *          "requirement"="[a-zA-Z0-9-]+",
     *          "description"="the id of the needed status"
     *      }
     *  },
     *  headers={
     *      {
     *          "name"="apikey",
     *          "description"="the user api key",
     *          "required"=true
     *      }
     *  },
     *  statusCodes={
     *      200="Returned on success",
     *      403="Returned on apikey authentication error",
     *      404="Returned if the specified status id is not found"
     *  }
     * )
     */
    public function getAction(Request $request, $id)
    {
        $status = $this->getRepository()->find($id);

        if ($status === null) {
            $error = new NotFoundError('status', $id, $request->getUri());
            return new JsonResponse(
                $this->getErrorParser()->parse($error),
                404
            );
        }

        return new JsonResponse(
            $this->getStatusParser()->parse($status),
            200,
            array(),
            true
        );
    }

    /**
     * @return StatusJsonParser
     */
    private function getStatusParser()
    {
        return $this->get('json_parser-status');
    }

    /**
     * @return EntityRepository
     */
    private function getRepository()
    {
        if ($this->repository !== null) {
            return $this->repository;
        }

        $this->repository = $this->getManager()->getRepository(Status::class);
        return $this->repository;
    }

    /**
     * @return EntityManager
     */
    private function getManager()
    {
        if ($this->manager !== null) {
            return $this->manager;
        }

        $this->manager = $this->get('doctrine.orm.entity_manager');
        return $this->manager;
    }

    /**
     * @return ErrorParser
     */
    private function getErrorParser()
    {
        return $this->get('error_parser');
    }
}
