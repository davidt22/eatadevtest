<?php


namespace AppBundle\Model;


use JMS\Serializer\Annotation as Serializer;

class Token
{
    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("access_token")
     */
    private $accessToken;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("refresh_token")
     */
    private $refreshToken;

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }
}