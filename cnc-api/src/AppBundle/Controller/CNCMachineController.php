<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * NOTE: All machines are identified by a MAC address.
 *
 * @Route("/cnc-machine", defaults={"_format"="json"})
 */
class CNCMachineController extends Controller
{
    /**
     * Endpoint used by a CNC machine to register itself.
     *
     * A machine calls this endpoint once it's up and running, and sends its IP and MAC addresses.
     *
     * After initial registration this API issues a call to a Machine's API to fetch more details.
     *
     * Periodically, this API will check if each machine is 'alive' based on the submitted IP address,
     * by calling /ping on each.
     *
     * @Route("/register", name="register")
     * @Method({"POST"})
     *
     * @ApiDoc(
     *  resource=true
     * )
     */
    public function registerAction(Request $request)
    {
        // TODO: Add content.
        return $this->render('default/index.html.twig', array(
            'content' => array()
        ));
    }

    /**
     * Endpoint used by a CNC machine to update its own status.
     *
     * Once an event occurs on a machine, it must push the new status to this endpoint.
     *
     * @Route("/update-status", name="update-status")
     * @Method({"PATCH"})
     *
     * @ApiDoc(
     *  resource=true
     * )
     */
    public function updateStatusAction(Request $request)
    {
        // TODO: Add content.
        return $this->render('default/index.html.twig', array(
            'content' => array()
        ));
    }

    /**
     * Endpoint used for listing CNC machines.
     *
     * List all machines, or filter by status and others.
     *
     * @Route("/list", name="list-machines")
     * @Method({"GET"})
     *
     * @ApiDoc(
     *  resource=true
     * )
     */
    public function listMachinesAction(Request $request)
    {
        // TODO: Add content.
        return $this->render('default/index.html.twig', array(
            'content' => array()
        ));
    }

    /**
     * Endpoint used for deleting a CNC machine by its MAC address.
     *
     * Soft delete a CNC machine.
     *
     * @Route("/delete", name="delete-machine")
     * @Method({"DELETE"})
     *
     * @ApiDoc(
     *  resource=true
     * )
     */
    public function deleteMachineAction(Request $request)
    {
        // TODO: Add content.
        return $this->render('default/index.html.twig', array(
            'content' => array()
        ));
    }

    /**
     * Endpoint used for viewing a CNC machine details by MAC address.
     *
     * View CNC machine details by MAC address.
     *
     * @Route("/view", name="view-machine")
     * @Method({"GET"})
     *
     * @ApiDoc(
     *  resource=true
     * )
     */
    public function viewMachineAction(Request $request)
    {
        // TODO: Add content.
        return $this->render('default/index.html.twig', array(
            'content' => array()
        ));
    }

    /**
     * Endpoint used for editing a CNC machine details by MAC address.
     *
     * Edit CNC machine details by MAC address.
     *
     * @Route("/edit", name="edit-machine")
     * @Method({"PATCH"})
     *
     * @ApiDoc(
     *  resource=true
     * )
     */
    public function editMachineAction(Request $request)
    {
        // TODO: Add content.
        return $this->render('default/index.html.twig', array(
            'content' => array()
        ));
    }

    /**
     * Force ping of a machine by MAC address.
     *
     * Forcefully check the status of a machine.
     *
     * @Route("/force-ping", name="force-ping")
     * @Method({"POST"})
     *
     * @ApiDoc(
     *  resource=true
     * )
     */
    public function forcePingAction(Request $request)
    {
        // TODO: Add content.
        return $this->render('default/index.html.twig', array(
            'content' => array()
        ));
    }
}
