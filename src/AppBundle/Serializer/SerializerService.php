<?php

namespace AppBundle\Serializer;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;

class SerializerService
{
    const JSON_FORMAT = 'json';

    /** @var SerializerInterface $serializer */
    private $serializer;

    /**
     * SerializerService constructor.
     * @param $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param $data
     * @param $type
     * @param string $format
     * @param DeserializationContext|null $context
     *
     * @return array|\JMS\Serializer\scalar|mixed|object
     */
    public function deserialize($data, $type, $format = self::JSON_FORMAT, DeserializationContext $context = null)
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }

    /**
     * @param $data
     * @param $format
     *
     * @return mixed|string
     */
    public function serialize($data, $format = self::JSON_FORMAT)
    {
        return $this->serializer->serialize($data, $format);
    }

}