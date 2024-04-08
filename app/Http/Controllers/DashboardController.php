<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;



use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $employeeFieldsIndexes = array(
            'ID'=>17,
            'photo'=>15,
            'firstname' => 1,
            'lastname' => 2,
            'designation' => 4,
            'email' => 7,
            'department'=>8,
            'manager'=>13,
            'jobTitle'=>4,
            'division'=>10,            
        );

        //Count of Users Without Super Admin
        $usersCount = User::whereHas('role', function($q) {
            $q->where('name', '!=', 'SUPER_ADMIN');
        })->count();

        $apiKey = '40d056dd98d048b1d50c46392c77bd2bbbf0431f';
        $endpoint = 'https://api.bamboohr.com/api/gateway.php/clhmentalhealth/v1/employees/directory';
    
        $response = file_get_contents($endpoint, false, stream_context_create([
            'http' => [
                'header' => "Authorization: Basic " . base64_encode($apiKey . ':x')
            ]
        ]));
    
        $xml = simplexml_load_string($response);

        // Convert the XML object to an associative array
        $employees = json_encode($xml);  
        
      
        $dataArray = json_decode($employees, true);
         
        $empMainArr = [];
        $employeeFields = $dataArray['fieldset'];
       
        $employeeFields = $employeeFields['field'];
        //dump($dataArray); dd();
        $employees = $dataArray['employees'];
        $employees = $employees['employee'];
        
        $empMainArr = array();
        $empIdsArr = array();
        $i = 0;
        foreach($employees as $empKeys=> $emp){
            $empID = $emp['@attributes']['id'];
            $empMainArr[$i]['ID'] = $empID;
            $empMainArr[$i]['photo'] = $this->checkIfImageExists($emp['field'][$employeeFieldsIndexes['photo']]);
            $empMainArr[$i]['firstname'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['firstname']]);
            $empMainArr[$i]['lastname'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['lastname']]);
            $empMainArr[$i]['designation'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['designation']]);
            $empMainArr[$i]['email'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['email']]);
            $empMainArr[$i]['department'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['department']]);
            $empMainArr[$i]['manager'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['manager']]);
            $empMainArr[$i]['jobTitle'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['jobTitle']]);
            $empMainArr[$i]['division'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['division']]);
            //$empMainArr[$i]['profile'] = 'https://clhmentalhealth.bamboohr.com/employees/employee.php?id='.$empID;
            $empIdsAr[] = $empID;
            $i++;

            
            // foreach($employeeFields as $key=>$fieldName){
                
            //     //CASE: in some fields it's further a blank array if anything is not provided against these keys
            //     if(!is_array($emp['field'][$key])){
            //         $empMainArr[$empKeys][$key] = $emp['field'][$key];
            //         //CASE: We will check if any array key contains some value of image
            //         if (strpos($emp['field'][$key], "https://images") === 0 || strpos($emp['field'][$key], "https://resources") === 0) {                        
            //            // echo $emp['field'][$key];
            //            $empMainArr[$empKeys][$key] = '<img src="'.$emp['field'][$key].'" alt="Photo URL">';
            //         }
                    
            //     }else{
            //         $empMainArr[$empKeys][$key] = 'N/A';
            //     }
                
            // }
            
        }  

        $empIdsAr = json_encode($empIdsAr);     
        return view('dashboard.index',compact('usersCount', 'empMainArr', 'employeeFieldsIndexes', 'empIdsAr'));
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

//dump($base64Image);dd('--');
  //      $imgXml = simplexml_load_string($imgResponse);
    //    $employeeImgData = json_encode($imgXml);       
      //  $dataImgArray = json_decode($employeeImgData, true);
        //print_r($dataImgArray); dd('-');
    
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
