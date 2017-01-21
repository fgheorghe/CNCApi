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
 * @Route("/stl", defaults={"_format"="json"})
 */
class STLController extends Controller
{
    /**
     * Creates a new STL file record.
     *
     * @Route("/upload", name="stl-upload")
     * @Method({"POST"})
     * @ApiDoc(
     *  resource=false
     * )
     */
    public function uploadAction(Request $request)
    {
        // TODO: Implement.
    }

    /**
     * Updates a single field against an STL file
     * identified by a numeric identifier.
     *
     * @Route("/{id}", name="stl-update")
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
     * Returns a specific STL file.
     *
     * @Route("/{id}", name="stl-fetch")
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
     * Lists STL files.
     *
     * @Route("/", name="stl-list")
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
