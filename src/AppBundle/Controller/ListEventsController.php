<?php

namespace AppBundle\Controller;

use AppBundle\Serializer\SerializerService;
use AppBundle\Service\RequestManagerAPIInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/event")
 */
class ListEventsController extends Controller
{
    /** @var RequestManagerAPIInterface $requestManager */
    private $requestManager;

    /** @var SerializerService */
    private $serializer;

    /**
     * DefaultController constructor.
     *
     * @param RequestManagerAPIInterface $requestManager
     * @param SerializerService $serializerService
     */
    public function __construct(RequestManagerAPIInterface $requestManager, SerializerService $serializerService)
    {
        $this->requestManager = $requestManager;
        $this->serializer = $serializerService;
    }

    /**
     * @Route("/list", name="events_list")
     */
    public function listAction(Request $request)
    {
        $pathUrl = 'events';
        $response = $this->requestManager->sendRequest(Request::METHOD_GET, $pathUrl);
        $events = $this->serializer->deserialize($response, 'ArrayCollection<AppBundle\Model\Event>');


        // replace this example code with whatever you need
        return $this->render('event/list.html.twig', array(
            'events' => $events
        ));
    }
}
