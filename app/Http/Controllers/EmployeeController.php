<?php

namespace App\Http\Controllers;

use App\Services\BambooHrService;

class EmployeeController extends Controller
{
    protected $bambooHrService;

    public function __construct(BambooHrService $bambooHrService)
    {
        $this->bambooHrService = $bambooHrService;
    }

    public function show($employeeId)
    {
        // Use the BambooHrService to get employee data
        $employeeData = $this->bambooHrService->getEmployeeData($employeeId);
        // dd('---');

        // Process the data or return it to the view
        return view('employee.show', ['employeeData' => $employeeData]);
    }

    public function employeDetail($empId){

        $apiKey = '40d056dd98d048b1d50c46392c77bd2bbbf0431f';
        $params = 'firstName,lastName,jobTitle,workPhone,mobilePhone,workEmail,department,location,division,supervisor,photoUrl,canUploadPhoto';
        $endpoint = 'https://40d056dd98d048b1d50c46392c77bd2bbbf0431f:x@api.bamboohr.com/api/gateway.php/clhmentalhealth/v1/employees/'.$empId.'/?fields='.$params.'&format=JSON';
    
        $response = file_get_contents($endpoint, false, stream_context_create([
            'http' => [
                'header' => "Authorization: Basic " . base64_encode($apiKey . ':x')
            ]
        ]));

        //call employee image api
        $imageEndpoint = 'https://40d056dd98d048b1d50c46392c77bd2bbbf0431f:x@api.bamboohr.com/api/gateway.php/clhmentalhealth/v1/employees/'.$empId.'/photo/small';
        $headers = [
            'Authorization: Basic ' . base64_encode($apiKey . ':x'),
            'Content-Type: image/jpeg'
            // Add more headers as needed
        ];
        $imgResponse = file_get_contents($imageEndpoint, false, stream_context_create([
            'http' => [
                'header' => implode("\r\n", $headers),
            ]
        ]));


        if ($imgResponse !== false) {
            // Get the MIME type of the image
            $imageInfo = getimagesizefromstring($imgResponse);
            $imageMimeType = $imageInfo['mime'];
        
            // Generate a base64 encoded string of the image
            $base64Image = 'data:' . $imageMimeType . ';base64,' . base64_encode($imgResponse);
        }
    
        $xml = simplexml_load_string($response);
        $employeeData = json_encode($xml);       
        $dataArray = json_decode($employeeData, true);
        $empKeyArr = explode(',', $params);
        $empData = [];
        foreach($dataArray['field'] as $key=> $field){
            $empData[$empKeyArr[$key]]= $this->checkIfArray($field);            
        }
        $empData['ID'] = $empId;
        //dump($empData);
        // dd('--');
        return view('dashboard.employee',compact('empData', 'base64Image'));
        
    }

    
    private function checkIfArray($arrayObj)
    {
        $finalVal = '';
        if(!is_array($arrayObj)){
            $finalVal = $arrayObj;
        }else{
            $finalVal = 'N/A';
        }
        return $finalVal;
    }

    private function checkIfImageExists($arrayObj){
        $finalImage = '';
        if(!is_array($arrayObj)){
            if (strpos($arrayObj, "https://images") === 0 || strpos($arrayObj, "https://resources") === 0) {                        
                
                $finalImage = '<img src="'.$arrayObj.'" alt="Photo URL">';
            }
        }else{
            $finalImage = 'N/A';
        }
        return $finalImage;
    }
}
