<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * Test action
     *
     * This route is used to test the current loggin of the api.
     *
     * @Route("", name="apiTest")
     * @Method({"GET"})
     * @ApiDoc(
     *  description="Loggin confirmation route",
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
    public function testAction()
    {
        return new JsonResponse(array('connection' => 'ok', 'loggedAs' => $this->getUser()->getUsername()));
    }
}