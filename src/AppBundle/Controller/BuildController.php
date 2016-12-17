<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Build;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Errors\NotFoundError;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\DTO\BuildDto;
use AppBundle\Form\BuildForm;
use AppBundle\Errors\ConflictError;
use AppBundle\Errors\UnprocessableError;
use AppBundle\Errors\NoContentError;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Controller\Abstracts\AbstractApiController;

/**
 * @Route("/api")
 */
final class BuildController extends AbstractApiController
{

    /**
     * Get builds
     *
     * This route is used to get all of the existing builds.
     *
     * @Route("/builds", name="getBuilds")
     * @Method({"GET"})
     * @ApiDoc(
     *  description="Get all builds",
     *  section="build",
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
        $builds = $this->getRepository(Build::class)->findAll();
        $parser = $this->getJsonParser('build');

        return new JsonResponse(
            $parser->parseArray((array)$builds),
            200,
            array(),
            true
        );
    }

    /**
     * Get build
     *
     * This route is used to get a specified build resource.
     *
     * @Route("/build/{id}", name="getBuild")
     * @Method({"GET"})
     * @ApiDoc(
     *  description="Get a build",
     *  section="build",
     *  requirements={
     *      {
     *          "name"="id",
     *          "dataType"="guid",
     *          "requirement"="[a-zA-Z0-9-]+",
     *          "description"="the id of the needed build"
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
     *      404="Returned if the specified build id is not found"
     *  }
     * )
     */
    public function getAction(Request $request, $id)
    {
        $build = $this->getRepository()->find($id);

        if ($build === null) {
            $error = new NotFoundError('build', $id, $request->getUri());
            return new JsonResponse(
                $this->getErrorParser()->parse($error),
                404
            );
        }

        return new JsonResponse(
            $this->getBuildParser()->parse($build),
            200,
            array(),
            true
        );
    }

    /**
     * Post build
     *
     * This route is used to create a new build.
     *
     * @Route("/build", name="postBuild")
     * @Method({"POST"})
     * @ApiDoc(
     *  description="Create a build",
     *  section="build",
     *  input="AppBundle\Form\BuildForm",
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
     *      409="Returned if the given data create a conflict with the name of an existing build, in the same project",
     *      422="Returned if the given data cannot be processed"
     *  }
     * )
     */
    public function postAction(Request $request)
    {
        $dto = new BuildDto();

        $form = $this->createForm(BuildForm::class, $dto);

        if (!$request->request->has($form->getName())) {
            $error = new NoContentError($form->getName(), $request->getUri());
            return new JsonResponse(
                $this->getErrorParser()->parse($error),
                204
            );
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $conflictBuilder = $this->getRepository()->createQueryBuilder('b');
            $conflictBuilder->where($conflictBuilder->expr()->eq('b.name', '?1'));
            $conflictBuilder->andWhere($conflictBuilder->expr()->eq('b.project', '?2'));
            $conflictBuilder->setParameter(1, $dto->name);
            $conflictBuilder->setParameter(2, $dto->project);
            $existing = $conflictBuilder->getQuery()->getOneOrNullResult();

            if ($existing !== null) {
                $error = new ConflictError(
                    'build',
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
                $this->getBuildParser()->parse($entity),
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
}
