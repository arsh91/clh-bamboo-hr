<?php

namespace App\Http\Controllers;

use App\Services\BambooHrService;
use \BambooHR\API\BambooAPI;

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

        $empFieldsArray = array('firstName,lastName,jobTitle,workPhone,mobilePhone,workEmail,department,location,division,supervisor,photoUrl,canUploadPhoto');

        $bhr = new BambooAPI("clhmentalhealth");
        $bhr->setSecretKey("40d056dd98d048b1d50c46392c77bd2bbbf0431f");
        $response = $bhr->getDirectory();
        $getEmployee = $bhr->getEmployee($empId, $empFieldsArray);
        if($getEmployee->isError()) {
        trigger_error("Error communicating with BambooHR: " . $getEmployee->getErrorMessage());
        }

        $getEmployeeData = $getEmployee->getContent();
    

       //FOR EMPLOYEE IMAGE
        $imgResponse = $bhr->downloadEmployeePhoto($empId, 'small', array(["width" => 100], ["height" => 100]));
        $imgResponse = $imgResponse->getContent();
        if ($imgResponse !== false) {
            // Get the MIME type of the image
            $imageInfo = getimagesizefromstring($imgResponse);
            $imageMimeType = $imageInfo['mime'];
        
            // Generate a base64 encoded string of the image
            $base64Image = 'data:' . $imageMimeType . ';base64,' . base64_encode($imgResponse);
        }
        
        //GET INFO OF JOB TABLE
        $getJobInfo = $bhr->getTable($empId, 'jobInfo');
        if($getJobInfo->isError()) {
            trigger_error("Error communicating with BambooHR: " . $getJobInfo->getErrorMessage());
        }
        $getJobInfo = $getJobInfo->getContent();
        $getJobInfo = json_encode($getJobInfo);       
        $getJobInfo = json_decode($getJobInfo, true);
        $jobFields = [];

        foreach ($getJobInfo as $job) {
            foreach($job as $item){
                // Check if the item has the 'field' key
                if (isset($item['field'])) {
                    // Add the 'field' array to the $fields array
                    $jobFields[] = $item['field'];
                }
            }
        }

        //GET EMERGENCY TAB DATA
        $getEmergencyContacts = $bhr->getTable($empId, 'emergencyContacts');
        
        if($getEmergencyContacts->isError()) {
            trigger_error("Error communicating with BambooHR: " . $getEmergencyContacts->getErrorMessage());
        }
        $getEmergencyContacts = $getEmergencyContacts->getContent();
        
        $getEmergencyContacts = json_encode($getEmergencyContacts);       
        $getEmergencyContacts = json_decode($getEmergencyContacts, true);
        $emergencyContacts = [];

        $emergency = $getEmergencyContacts['row'];
        if (isset($emergency['field'])) {
            foreach($emergency['field'] as $key=> $field){
                $emergencyContacts[] = $this->checkIfArray($field);
            }     
        }

        $params = 'firstName,lastName,jobTitle,workPhone,mobilePhone,workEmail,department,location,division,supervisor,photoUrl,canUploadPhoto';
        
        $employeeData = json_encode($getEmployeeData);       
        $dataArray = json_decode($employeeData, true);
        $empKeyArr = explode(',', $params);
      
        $empData = [];
        foreach($dataArray['field'] as $key=> $field){
            $empData[$empKeyArr[$key]]= $this->checkIfArray($field);            
        }
        $empData['ID'] = $empId;
        //dump($empData);
        // dd('--');
        return view('dashboard.employee',compact('empData', 'base64Image', 'jobFields', 'emergencyContacts'));
        
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
