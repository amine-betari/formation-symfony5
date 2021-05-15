<?php

namespace App\Service;

use DateTime;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetData
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fromGouv(): string
    {
        $response = $this->client->request(
            'GET',
            'https://www.data.gouv.fr/fr/datasets/r/16cb2df5-e9c7-46ec-9dbf-c902f834dab1'
        );

        $lastData = array_key_last($response->toArray());

        $date = new DateTime($response->toArray()[$lastData]['date']);
        $nombre = $response->toArray()[$lastData]['totalVaccines'];
        $code = $response->toArray()[$lastData]['code'];
        $name = $response->toArray()[$lastData]['nom'];

        return 'Au ' . $date->format('d/m/Y') . ', il y a ' . $nombre . ' personnes de vaccinées en France 
        !  => Département '. $code. ' Nome du département => '. $name;
    }

}