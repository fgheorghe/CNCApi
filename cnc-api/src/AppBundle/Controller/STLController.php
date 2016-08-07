<?php
namespace AppBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use AppBundle\Service\STL;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * @Route("/stl", defaults={"_format"="json"})
 */
class STLController extends Controller
{
    /**
     * @var ContainerInterface
     */
    protected $container;

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

        /**
         * @var STL $stl
         */
        $stl = $this->container->get('stl');
        $stl->upload($content);

        // TODO: Add error handling.
        return $this->render('default/index.html.twig', array(
            'content' => array(
                "success" => true
            )
        ));
    }
}