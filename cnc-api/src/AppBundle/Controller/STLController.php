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
        // TODO: Remove redundant code -> used by upload command.
        $file = $request->files->get('file');
        $content = file_get_contents($file->getRealPath());

        $stl = new STL(
            $this->container,
            new STLFileReader($content),
            $this->container->get('doctrine')->getManager()->getConnection(),
            // As per: https://github.com/php-amqplib/php-amqplib/blob/master/demo/amqp_publisher.php
            new AMQPStreamConnection(
                $this->container->getParameter('rabbit_mq_host'),
                $this->container->getParameter('rabbit_mq_port'),
                $this->container->getParameter('rabbit_mq_user'),
                $this->container->getParameter('rabbit_mq_password'),
                $this->container->getParameter('rabbit_mq_vhost')
            ),
            $this->container->getParameter('rabbit_mq_stl_queue_name'),
            $this->container->getParameter('rabbit_mq_stl_exchange_name')
        );

        try {
            $stl->upload();
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
        // TODO: Move to a service...don't allow SQL in controllers. This is an experiment.
        $name = $request->get('name');

        $result = $this->getDoctrine()->getConnection()
            ->executeQuery(
                "SELECT stl_object_coordinates FROM stl_objects WHERE stl_object_name = :name LIMIT 1",
                array(
                    "name" => $name
                )
            )->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('default/index.html.twig', array(
            'content' => array(
                "data" => json_decode($result[0]["stl_object_coordinates"])
            )
        ));
    }
}
