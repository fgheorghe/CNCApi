<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/", defaults={"_format"="json"})
 */
class DefaultController extends Controller
{
    /**
     * Version endpoint.
     *
     * Returns a JSON object with the following format:
     *
     * {"version":
     *  {"major":0,
     *  "minor":0,
     *  "revision":0}
     * }
     *
     * @Route("/ehlo", name="ehlo")
     * @Method({"GET"})
     *
     * @ApiDoc(
     *  resource=true
     * )
     */
    public function ehloAction(Request $request)
    {
        // Return version object.
        return $this->render('default/index.html.twig', array(
            'content' => array(
                "version" => $this->container->getParameter('version')
            )
        ));
    }
}
