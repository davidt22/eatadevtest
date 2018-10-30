<?php


namespace AppBundle\Service;


interface RequestManagerAPIInterface
{
    public function sendRequest($method, $pathUrl, $data = array());
}