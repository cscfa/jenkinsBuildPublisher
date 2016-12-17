<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Repository\ProjectRepository;
use AppBundle\Entity\Project;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Parser\ProjectJsonParser;
use AppBundle\DTO\Parser\ProjectDtoParser;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\DTO\ProjectDto;
use AppBundle\Form\ProjectForm;
use AppBundle\Parser\ErrorParser;
use AppBundle\Errors\UnprocessableError;
use AppBundle\Parser\FormParser;
use AppBundle\Errors\NotFoundError;
use AppBundle\Errors\ConflictError;
use AppBundle\Errors\NoContentError;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Route("/api")
 */
class ProjectController extends Controller
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var ProjectRepository
     */
    private $repository;

    /**
     * Get projects
     *
     * This route is used to get all of the existing projects.
     *
     * @Route("/projects", name="getProjects")
     * @Method({"GET"})
     * @ApiDoc(
     *  description="Get all projects",
     *  section="project",
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
        $projects = $this->getRepository()->findAll();
        $parser = $this->getProjectParser();

        return new JsonResponse(
            $parser->parseArray((array)$projects),
            200,
            array(),
            true
        );
    }

    /**
     * Get project
     *
     * This route is used to get a specified project resource.
     *
     * @Route("/project/{id}", name="getProject")
     * @Method({"GET"})
     * @ApiDoc(
     *  description="Get a project",
     *  section="project",
     *  output="array",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="guid",
     *          "requirement"="[a-zA-Z0-9-]+",
     *          "description"="the id of the needed project"
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
     *      404="Returned if the specified project id is not found"
     *  }
     * )
     */
    public function getAction(Request $request, $id)
    {
        $project = $this->getRepository()->find($id);

        if ($project === null) {
            $error = new NotFoundError('project', $id, $request->getUri());
            return new JsonResponse(
                $this->getErrorParser()->parse($error),
                404
            );
        }

        return new JsonResponse(
            $this->getProjectParser()->parse($project),
            200,
            array(),
            true
        );
    }

    /**
     * Post project
     *
     * This route is used to create a new project.
     *
     * @Route("/project", name="postProject")
     * @Method({"POST"})
     * @ApiDoc(
     *  description="Create a project",
     *  section="project",
     *  input="AppBundle\Form\ProjectForm",
     *  headers={
     *      {
     *          "name"="apikey",
     *          "description"="the user api key",
     *          "required"=true
     *      }
     *  },
     *  statusCodes={
     *      201="Returned on created",
     *      204="Returned if no content can be found in the request",
     *      403="Returned on apikey authentication error",
     *      409="Returned if the given data create a conflict with the name of an existing project",
     *      422="Returned if the given data cannot be processed"
     *  }
     * )
     */
    public function postAction(Request $request)
    {
        $dto = new ProjectDto();

        $form = $this->createForm(ProjectForm::class, $dto);

        if (!$request->request->has($form->getName())) {
            $error = new NoContentError($form->getName(), $request->getUri());
            return new JsonResponse(
                $this->getErrorParser()->parse($error),
                204
            );
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $existing = $this->getRepository()->findOneByName($dto->name);
            if ($existing !== null) {
                $error = new ConflictError(
                    'project',
                    'name',
                    $dto->name,
                    $request->getUri(),
                    json_encode($request->request->all())
                );
                return new JsonResponse(
                    $this->getErrorParser()->parse($error),
                    409
                );
            }

            $entity = $this->getDtoParser()->toEntity($dto);

            $this->getManager()->persist($entity);
            $this->getManager()->flush();

            return new JsonResponse(
                $this->getProjectParser()->parse($entity),
                201,
                array(),
                true
            );
        }

        $error = new UnprocessableError(
            $this->getFormParser()->getErrorAsString($form),
            $request->getUri(),
            json_encode($request->request->all())
        );
        return new JsonResponse(
            $this->getErrorParser()->parse($error),
            422
        );
    }

    /**
     * @return ProjectDtoParser
     */
    private function getDtoParser()
    {
        return $this->get('dto_parser-project');
    }

    /**
     * @return ProjectJsonParser
     */
    private function getProjectParser()
    {
        return $this->get('json_parser-project');
    }

    /**
     * @return ProjectRepository
     */
    private function getRepository()
    {
        if ($this->repository !== null) {
            return $this->repository;
        }

        $this->repository = $this->getManager()->getRepository(Project::class);
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

    /**
     * @return FormParser
     */
    private function getFormParser()
    {
        return $this->get('form_parser');
    }
}
