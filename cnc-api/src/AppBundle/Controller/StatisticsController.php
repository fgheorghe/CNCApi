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
use Symfony\Component\HttpFoundation\AcceptHeader;

/**
 * @Route("/statistics", defaults={"_format"="json"})
 */
class StatisticsController extends Controller
{
    /**
     * Returns machine identifiers and their total
     * number of hours online / offline since first registration.
     *
     * @Route("/machines", name="machine-stats")
     * @Method({"GET"})
     * @ApiDoc(
     *  resource=false
     * )
     */
    public function machineStatsAction(Request $request)
    {
        // TODO: Implement.
    }

    /**
     * Returns statistics of time to complete for each
     * STL file “printed” or “milled”.
     *
     * @Route("/jobs", name="job-stats")
     * @Method({"GET"})
     * @ApiDoc(
     *  resource=false
     * )
     */
    public function jobStatsAction(Request $request)
    {
        // TODO: Implement.
    }

    private function csv_format($array) {
        return implode("\n", $array); // TODO: Implement.
    }

    /**
     * Returns statistics of time to process and
     * convert each STL file to G-Code.
     *
     * @Route("/stl", name="stl-stats")
     * @Method({"GET"})
     * @ApiDoc(
     *  resource=false
     * )
     */
    public function stlStatsAction(Request $request)
    {
        // TODO: Refactor.
        $data = array(); // TODO: Get using a reporting class.

        // Based on: http://symfony.com/doc/current/components/http_foundation.html

        // Get Accept header value.
        $accept = AcceptHeader::fromString($request->headers->get('Accept'));

        // Format data according to accepted format.
        switch (strtolower($accept)) {
            case "text/csv":
                $response = $this->csv_format($data);
                break;
            default:
                $response = json_encode($data);
        }

        // Set response headers.
        header('Content-Type: ' . $accept);
        echo $response;

        // Stop execution.
        exit();
    }

}
