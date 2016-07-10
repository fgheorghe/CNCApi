<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/", defaults={"_format"="json"})
 */
class DefaultController extends Controller
{
    /**
     * @Route("/ehlo", name="ehlo")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'content' => array(
                "version" => $this->container->getParameter('version')
            )
        ));
    }
}
