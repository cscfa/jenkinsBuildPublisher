<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\BuildFile;
use AppBundle\Parser\BuildFileJsonParser;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Errors\NotFoundError;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\DTO\Parser\BuildFileDtoParser;
use AppBundle\DTO\BuildFileDto;
use AppBundle\Form\BuildFileForm;
use AppBundle\Errors\NoContentError;
use AppBundle\Errors\ConflictError;
use AppBundle\Errors\UnprocessableError;

/**
 * @Route("/api")
 */
class BuildFileController extends Controller
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
     * Get build files
     *
     * This route is used to get all of the existing build files.
     *
     * @Route("/build_files", name="getBuildFiles")
     * @Method({"GET"})
     * @ApiDoc(
     *  description="Get all build files",
     *  section="build_file",
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
        $builds = $this->getRepository()->findAll();
        $parser = $this->getBuildFileParser();

        return new JsonResponse(
            $parser->parseArray((array)$builds),
            200,
            array(),
            true
        );
    }

    /**
     * Get build file
     *
     * This route is used to get a specified build file resource.
     *
     * @Route("/build_file/{id}", name="getBuildFile")
     * @Method({"GET"})
     * @ApiDoc(
     *  description="Get a build file",
     *  section="build_file",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="guid",
     *          "requirement"="[a-zA-Z0-9-]+",
     *          "description"="the id of the needed build file"
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
     *      404="Returned if the specified build file id is not found"
     *  }
     * )
     */
    public function getAction(Request $request, $id)
    {
        $build = $this->getRepository()->find($id);

        if ($build === null) {
            $error = new NotFoundError('build_file', $id, $request->getUri());
            return new JsonResponse(
                $this->getErrorParser()->parse($error),
                404
            );
        }

        return new JsonResponse(
            $this->getBuildFileParser()->parse($build),
            200,
            array(),
            true
        );
    }

    /**
     * Post build file
     *
     * This route is used to create a new build file.
     *
     * @Route("/build_file", name="postBuildFile")
     * @Method({"POST"})
     * @ApiDoc(
     *  description="Create a build file",
     *  section="build_file",
     *  input="AppBundle\Form\BuildFileForm",
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
     *      409="Returned if the given data create a conflict with the name of an existing build file, in the same build",
     *      422="Returned if the given data cannot be processed"
     *  }
     * )
     */
    public function postAction(Request $request)
    {
        $dto = new BuildFileDto();

        $form = $this->createForm(BuildFileForm::class, $dto);

        if (!$request->request->has($form->getName())) {
            $error = new NoContentError($form->getName(), $request->getUri());
            return new JsonResponse(
                $this->getErrorParser()->parse($error),
                204
            );
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $conflictBuilder = $this->getRepository()->createQueryBuilder('bf');
            $conflictBuilder->where($conflictBuilder->expr()->eq('bf.name', '?1'));
            $conflictBuilder->andWhere($conflictBuilder->expr()->eq('bf.build', '?2'));
            $conflictBuilder->setParameter(1, $dto->name);
            $conflictBuilder->setParameter(2, $dto->build);
            $existing = $conflictBuilder->getQuery()->getOneOrNullResult();

            if ($existing !== null) {
                $error = new ConflictError(
                    'build_file',
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
                $this->getBuildFileParser()->parse($entity),
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
     * @return BuildFileDtoParser
     */
    private function getDtoParser()
    {
        return $this->get('dto_parser-build_file');
    }

    /**
     * @return BuildFileJsonParser
     */
    private function getBuildFileParser()
    {
        return $this->get('json_parser-build_file');
    }

    /**
     * @return EntityRepository
     */
    private function getRepository()
    {
        if ($this->repository !== null) {
            return $this->repository;
        }

        $this->repository = $this->getManager()->getRepository(BuildFile::class);
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
