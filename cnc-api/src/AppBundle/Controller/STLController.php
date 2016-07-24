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
     * Raw STL file upload endpoint.
     *
     * @Route("/upload", name="stl-upload")
     * @Method({"POST"})
     * @ApiDoc(
     *  resource=false,
     *  parameters={
     *      {"name"="file", "dataType"="file", "required"=true, "description"="Raw STL file."}
     *  }
     * )
     */
    public function uploadAction(Request $request)
    {
        $file = $request->files->get('file');
        $content = file_get_contents($file->getRealPath());

        try {
            (new STLUtil())->upload($content, $this->container);
            return $this->render('default/index.html.twig', array(
                'content' => array(
                    "success" => true
                )
            ));
        } catch (Exception $ex) {
            return $this->render('default/index.html.twig', array(
                'content' => array(
                    "success" => false,
                    "reason" => $ex->getMessage()
                )
            ));
        }
    }

    /**
     * Get STL file coordinates as a JSON object.
     *
     * @Route("/coordinates/{name}", name="stl-coordinates-get")
     * @Method({"GET"})
     * @ApiDoc(
     *  resource=true
     * )
     */
    public function getCoordinatesAction(Request $request)
    {
        $name = $request->get('name');

        $coordinates = (new STLUtil())->getCoordinates($name, $this->getDoctrine());

        return $this->render('default/index.html.twig', array(
            'content' => array(
                "data" => $coordinates
            )
        ));
    }
}
