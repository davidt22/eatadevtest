<?php

namespace AppBundle\Controller;

use AppBundle\Model\Event;
use AppBundle\Serializer\SerializerService;
use AppBundle\Service\RequestManagerAPIInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/event")
 */
class ShowEventController extends Controller
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
     * @Route("/show/{id}", name="event_show")
     */
    public function showAction(Request $request, $id)
    {
        $pathUrl = 'events/'.$id;
        $response = $this->requestManager->sendRequest(Request::METHOD_GET, $pathUrl);
        $event = $this->serializer->deserialize($response, Event::class);


        // replace this example code with whatever you need
        return $this->render('event/show.html.twig', array(
            'event' => $event
        ));
    }
}
