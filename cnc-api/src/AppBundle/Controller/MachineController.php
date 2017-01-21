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
 * @Route("/machine", defaults={"_format"="json"})
 */
class MachineController extends Controller
{
    /**
     *
     * Adds a new machine to the database.
     *
     * @Route("/", name="create-machine")
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
     * Updates individual fields of the data model of a machine.
     *
     * @Route("/{id}", name="update-machine")
     * @Method({"PATCH"})
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
     * Sets a machine as offline.
     *
     * @Route("/{id}", name="delete-machine")
     * @Method({"DELETE"})
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
     * Allows visualisation of a machine details.
     *
     * @Route("/{id}", name="fetch-machine")
     * @Method({"GET"})
     * @ApiDoc(
     *  resource=false
     * )
     */
    public function fetchAction(Request $request)
    {
        // TODO: Implement.
    }

    /**
     *
     * Lists machines.
     *
     * @Route("/", name="list-machine")
     * @Method({"GET"})
     * @ApiDoc(
     *  resource=false
     * )
     */
    public function listAction(Request $request)
    {
        // TODO: Implement.
    }
}
