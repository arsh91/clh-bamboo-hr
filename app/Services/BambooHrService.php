<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class BambooHrService
{
    protected $client;
  //  protected $baseUrl = 'https://'.env('YOUR_API_KEY').':x@api.bamboohr.com/api/gateway.php';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://'.env('YOUR_API_KEY').':x@api.bamboohr.com/api/gateway.php',
            'headers' => [
                'Accept' => 'application/json',
                //'Authorization' => 'Basic ' . base64_encode(env('YOUR_API_KEY').':x'),
            ],
        ]);
       // dump($this->client); dd();
    }

    public function getEmployeeData($employeeId)
    {
       // $apiKey = '40d056dd98d048b1d50c46392c77bd2bbbf0431f:x@'; // Replace with your actual API key
       // $baseUrl = 'https://'.$apiKey.'api.bamboohr.com/api/gateway.php';
       // $endpoint = 'clhmentalhealth/v1/employees/directory';

        
        try {
            // Create a Guzzle client instance
            $client = new Client();
            $params = 'firstName,lastName,jobTitle,workPhone,mobilePhone,workEmail,department,location,division,supervisor,photoUrl,canUploadPhoto';
       
            $endpoint = 'https://40d056dd98d048b1d50c46392c77bd2bbbf0431f:x@api.bamboohr.com/api/gateway.php/clhmentalhealth/v1/employees/'.$employeeId.'/?fields='.$params.'&format=JSON';
    
            // Make a GET request
            $response = $client->get($endpoint);
        
            // Process the response
            $data = $response->getBody()->getContents();
        
            // Do something with the response data
            echo $data;
        } catch (RequestException $e) {
            // Handle Guzzle request exceptions
            if ($e->hasResponse()) {
                // Handle HTTP errors (e.g., 4xx or 5xx status codes)
                $response = $e->getResponse();
                echo 'HTTP Error: ' . $response->getStatusCode() . ' ' . $response->getReasonPhrase();
            } else {
                // Handle other Guzzle request errors (e.g., connection errors)
                echo 'Guzzle Request Error: ' . $e->getMessage();
            }
        } catch (\Exception $e) {
            // Handle other non-Guzzle exceptions
            echo 'Error: ' . $e->getMessage();
        }
        
        
    }
}
