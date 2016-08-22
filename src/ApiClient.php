<?php
namespace ArkonEvent\SendRegningApi;

use \GuzzleHttp\RequestOptions;

class ApiClient
{

    /**
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    const VERSION_LATEST = 'LATEST';

    public function __construct($username, $password, $sendRegningAccountId, $version = self::VERSION_LATEST, $baseUrl = 'https://www.sendregning.no/')
    {
        $httpOptions = [
            'base_uri' => $baseUrl,
            RequestOptions::HEADERS => [
                'Originator-Id' => $sendRegningAccountId,
                'Accept' => 'application/json'
            ],
            RequestOptions::AUTH => [
                $username,
                $password
            ]
        ];
        
        if ($version != self::VERSION_LATEST) {
            $httpOptions[RequestOptions::HEADERS]['API-Version'] = $version;
        }
        
        $this->client = new \GuzzleHttp\Client($httpOptions);
    }

    public function post($path, \stdClass $data)
    {
        $this->client->request('POST', $path, [
            RequestOptions::JSON => $data
        ]);
    }

    public function get($path)
    {
        $response = $this->client->request('GET', $path);
        
        return json_decode((string) $response->getBody());
    }

    public function getConstants()
    {
        return $this->get('/common/constants');
    }

    public function getInvoices()
    {
        return $this->get('/invoices');
    }
}