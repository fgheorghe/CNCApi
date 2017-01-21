<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\STL\STL;
use AppBundle\Service\STL\STLFileReader;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Exception;
use AppBundle\Service\STL\STLUtil;

/**
 * @Route("/job", defaults={"_format"="json"})
 */
class JobController extends Controller
{
    /**
     *
     * Create a new job for a given STL file id.
     *
     * @Route("/{stl-file-id}", name="create-job")
     * @Method({"POST"})
     * @ApiDoc(
     *  resource=false
     * )
     */
    public function createAction(Request $request)
    {
        // TODO: Implement.
    }

    /**
     *
     * Deletes an ongoing execution job for an STL file id.
     *
     * @Route("/{stl-file-id}", name="update-job")
     * @Method({"PATCH"})
     * @ApiDoc(
     *  resource=false
     * )
     */
    public function deleteAction(Request $request)
    {
        // TODO: Implement.
    }

    /**
     *
     * Updates an STL job for a given STL file id.
     *
     * @Route("/{stl-file-id}", name="delete-job")
     * @Method({"DELETE"})
     * @ApiDoc(
     *  resource=false
     * )
     */
    public function updateAction(Request $request)
    {
        // TODO: Implement.
    }

    /**
     *
     * Lists jobs.
     *
     * @Route("/", name="list-job")
     * @Method({"DELETE"})
     * @ApiDoc(
     *  resource=false
     * )
     */
    public function liastAction(Request $request)
    {
        // TODO: Implement.
    }
}
