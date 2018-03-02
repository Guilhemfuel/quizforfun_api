<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pusher\Pusher;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // Make some tests here

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/test", name="test")
     */
    public function getTestAction(Request $request)
    {
        $options = array(
            'cluster' => 'eu'
        );

        require(dirname(__FILE__).'/../../../vendor/autoload.php');

        $pusher = new Pusher(
            'b1ed0160cc1033ce4f54',
            'b8c985250b64b569c5c3',
            '464485',
            $options
        );

        $data['message'] = 'hello world';
        $xd = $pusher->trigger('my-channel', 'my-event', $data);

        return new Response(
            '<html><body>Lucky number: '.var_dump($pusher).'</body></html>'
        );
    }
}
