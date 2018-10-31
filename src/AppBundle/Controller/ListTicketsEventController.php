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
class ListTicketsEventController extends Controller
{
    /** @var RequestManagerAPIInterface $requestManager */
    private $requestManager;

    /** @var SerializerService */
    private $serializer;

    /**
     * ListTicketsEventController constructor.
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
     * @Route("/{id}/tickets", name="tickets_event_list")
     */
    public function listAction(Request $request, $id)
    {
        $pathUrl = 'events/'.$id.'/tickets';
        $response = $this->requestManager->sendRequest(Request::METHOD_GET, $pathUrl);
        $ticketsEvent = $this->serializer->deserialize($response, 'ArrayCollection<AppBundle\Model\Ticket>');

        // replace this example code with whatever you need
        return $this->render('event/listTickets.html.twig', array(
            'tickets' => $ticketsEvent
        ));
    }
}
