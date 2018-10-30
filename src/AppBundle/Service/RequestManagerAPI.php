<?php


namespace AppBundle\Service;


use AppBundle\Model\Token;
use AppBundle\Serializer\SerializerService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestManagerAPI implements RequestManagerAPIInterface
{
    const METHOD_AUTHENTICATION = 'authentication';
    const APPLICATION_JSON = 'application/json';
    const PROJECTION_FULL = 'projection=full';
    const APPLICATION_HAL_JSON = 'application/hal+json';

    const DEFAULT_SIZE = 100;
    const DEFAULT_PAGE = 0;

    /** @var string $apiKey */
    private $apiKey;

    /** @var string $apiBaseUrl */
    private $apiBaseUrl;

    /** @var SerializerService */
    private $serializer;

    /** @var Logger */
    private $logger;

    /**
     * RequestManagerAPI constructor.
     *
     * @param string $apiKey
     * @param string $apiBaseUrl
     * @param SerializerService $serializer
     * @param Logger $logger
     */
    public function __construct($apiKey, $apiBaseUrl, SerializerService $serializer, Logger $logger)
    {
        $this->apiKey = $apiKey;
        $this->apiBaseUrl = $apiBaseUrl;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    private function getAccessToken()
    {
        $pathUrl = 'oauth/token';
        $config = array(
            'headers' => array(
                'Content-Type' => self::APPLICATION_JSON,
                'api_key' => $this->apiKey
            ),
        );
        $client = new Client($config);
        $uri = $this->apiBaseUrl . '/' . $pathUrl;
        $data = array(
            'api_key' => 'uEmk89FbcZXXQ3QOL2XEiA=='
        );
        $options = array('body' => json_encode($data));
        $response = $client->post($uri, $options);

//        if ($response != null) {
//            $statusCode = $response->getStatusCode();
//
//            $this->logger->addNotice('sendRequest status token:', array('statusCode' => $statusCode));
//
//            if ($statusCode >= Response::HTTP_OK && $statusCode < Response::HTTP_BAD_REQUEST) {
//                $response->getBody()->rewind(); //Always rewind the Stream
                $content = $response->getBody()->getContents();

                /** @var Token $token */
                $token = $this->serializer->deserialize($content, Token::class);

                return $token->getAccessToken();
//            }
//
//            throw new \Exception('The status code of the Response is not valid');
//        }

        throw new \Exception('Error: Not allowed method for API.');

    }

    /**
     * @param string $method
     * @param string $pathUrl
     * @param array $data
     *
     * @throws \Exception
     */
    public function sendRequest($method = Request::METHOD_GET, $pathUrl = '', $data = array())
    {
        try {
            $accessToken = $this->getAccessToken();

            $config = array(
                'headers' => array(
                    'Content-Type' => self::APPLICATION_JSON
                ),
            );

            if($pathUrl == ''){
                throw new \Exception('Path url cannot be empty');
            }

            $client = new Client($config);
            $uri = $this->apiBaseUrl . '/' . $pathUrl . '?access_token=' . $accessToken;

            $options = array();
            if (!empty($data)) {
                $options = array(
                    'body' => json_encode($data)
                );
            }

            switch ($method) {
                case Request::METHOD_GET:

                    if (count($data) > 0) {

                        if ($this->isAssociative($data) == false) { //for routes without key=value like: .../products/2?projection=full
                            $uri .= '/' . $data[0];
                            $uri .= '?';
                        } else {                      //for routes with key=value like: .../products/id=2&type=3?projection=full
                            $uri .= '?';
                            foreach ($data as $key => $value) {
                                if(!is_null($value) && !empty($value)){
                                    $uri .= $key . '=' . $value;
                                    $uri .= '&';
                                }
                            }
                        }
                    }

                    $response = $client->get($uri, $options);
                    break;

                case Request::METHOD_POST:
                    $options = array();
                    if (!empty($data)) {

                        if(is_array($data)){ //Only encode arrays, others objects in json don't do anything
                            $data = json_encode($data);
                        }

                        $options = array('body' => $data);
                    }
                    $response = $client->post($uri, $options);
                    break;

                case Request::METHOD_PUT:
                    $response = $client->put($uri, $options);
                    break;

                case Request::METHOD_DELETE:
                    $response = $client->delete($uri, $options);
                    break;

                case Request::METHOD_PATCH:
                    $options = array();
                    if (!empty($data)) {

                        if(is_array($data)){ //Only encode arrays, others objects in json don't do anything
                            $data = json_encode($data);
                        }

                        $options = array('body' => $data);
                    }
                    $response = $client->patch($uri, $options);
                    break;

                default:
                    $response = null;
                    break;
            }

            if ($response != null) {
                $statusCode = $response->getStatusCode();

                $this->logger->addNotice('sendRequest status accessToken:', array('statusCode' => $statusCode));

                if ($statusCode >= Response::HTTP_OK && $statusCode < Response::HTTP_BAD_REQUEST) {
                    $response->getBody()->rewind(); //Always rewind the Stream
                    $content = $response->getBody()->getContents();

//                    if(!$decode) {
//                        return $content;
//                    }
//
//                    $content = json_decode($content);

                    return $content;
                }

                throw new \Exception('The status code of the Response is not valid');
            }

            throw new \Exception('Error: Not allowed method for API.');

        }catch(ClientException $clientExc){

            $message = $clientExc->getMessage();

            $errorData = array(
                'code' => $clientExc->getCode(),
                'error' => $message,
                'request' => $clientExc->getRequest(),
                'data' => $data,
                'uri' => $pathUrl,
                'method' => $method
            );

            $this->logger->addCritical('RequestManager CLIENT_EXCEPTION ERROR', $errorData);

            throw new \Exception($message, $clientExc->getCode());

        }catch(ServerException $exc){

            $message = $exc->getMessage();

            $errorData = array(
                'code' => $exc->getCode(),
                'error' => $message,
                'request' => $exc->getRequest(),
                'data' => $data,
                'uri' => $pathUrl,
                'method' => $method
            );

            $this->logger->addCritical('RequestManager SERVER EXCEPTION ERROR', $errorData);

            throw new \Exception($message, $exc->getCode());

        }catch(\Exception $e){

            $message = $e->getMessage();

            $errorData = array(
                'code' => $e->getCode(),
                'error' => $message,
                'data' => $data,
                'uri' => $pathUrl,
                'method' => $method
            );

            $this->logger->addCritical('RequestManager EXCEPTION ERROR', $errorData);

            throw new \Exception($message, $e->getCode());
        }
    }

    /**
     * @param $array
     *
     * @return bool
     */
    private function isAssociative($array)
    {
        return ($array !== array_values($array));
    }
}