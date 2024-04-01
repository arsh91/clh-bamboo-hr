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

    public function employeDetail($empId)
    {
        $base64Image = '';
        $empFieldsArray = array('firstName,lastName,jobTitle,workPhone,mobilePhone,workEmail,department,location,division,supervisor,employmentStatus');

        $bhr = new BambooAPI("clhmentalhealth");
        $bhr->setSecretKey("40d056dd98d048b1d50c46392c77bd2bbbf0431f");
        //$response = $bhr->getDirectory();
        $getEmployee = $bhr->getEmployee($empId, $empFieldsArray);
        if($getEmployee->isError()) {
        trigger_error("Error communicating with BambooHR: " . $getEmployee->getErrorMessage());
        }

        $getEmployeeData = $getEmployee->getContent();
        

       //FOR EMPLOYEE IMAGE
        $imgResponse = $bhr->downloadEmployeePhoto($empId, 'small', array(["width" => 100], ["height" => 100]));
        $imgResponse = $imgResponse->getContent();

        if ($imgResponse !== false &&  $imgResponse!= '') {
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
        $jobFields = $singleJobCase = [];

        foreach ($getJobInfo as $job) {
           
            if (isset($job['field'])) {
                foreach($job['field'] as $key=> $field){                    
                   $singleJobCase[] = $this->checkIfArray($field);            
                }
                $jobFields[] =  $singleJobCase;
            }else{
                foreach($job as $kk=> $item){                    
                    // Check if the item has the 'field' key
                    if (isset($item['field'])) {
                        foreach($item['field'] as $key=> $field){
                            
                        $jobFields[$kk][] = $this->checkIfArray($field);            
                        }
                    }
                }
            }
        } 
      // dump($jobFields); dd('--');
        //GET EMERGENCY TAB DATA
        $getEmergencyContacts = $bhr->getTable($empId, 'emergencyContacts');
        
        if($getEmergencyContacts->isError()) {
            trigger_error("Error communicating with BambooHR: " . $getEmergencyContacts->getErrorMessage());
        }
        $getEmergencyContacts = $getEmergencyContacts->getContent();
        
        $getEmergencyContacts = json_encode($getEmergencyContacts);       
        $getEmergencyContacts = json_decode($getEmergencyContacts, true);
       
        $emergencyContacts = [];
        $emptyEmeregencyFields = [];
       
        if(count($getEmergencyContacts) > 0){
            $emergency = $getEmergencyContacts['row'];        
            if (isset($emergency['field'])) {
                foreach($emergency['field'] as $key=> $field){
                    $emergencyContacts[] = $this->checkIfArray($field);
                    $emptyEmeregencyFields[] = $this->checkEmptyFields($field);
                }     
            }
        }

        //return the empty fileds 
        $emptyEmeregencyFields = array_filter($emptyEmeregencyFields, function($value) {
            return $value !== "";
        });

         //dump($emergencyContacts); 
        // dump($emptyEmeregencyFields); dd('-----');

        $params = 'firstName,lastName,jobTitle,workPhone,mobilePhone,workEmail,department,location,division,supervisor,employmentStatus';
        
        $employeeData = json_encode($getEmployeeData);       
        $dataArray = json_decode($employeeData, true);
        $empKeyArr = explode(',', $params);
      
        $empData = [];
        foreach($dataArray['field'] as $key=> $field){
            $empData[$empKeyArr[$key]]= $this->checkIfArray($field);            
        }
        $empData['ID'] = $empId;
       // dump($empData);  dd('----');

       //we will get the empty fields from personal tab of an employee
       $blankPersonalFields = $this->getPersonalBlankFields($empId);

       //get the empty fields array from JOB tab of an employee
       $blankJobFields = $this->getJobBlankFields($empId);

        return view('dashboard.employee',compact('empData', 'base64Image', 'jobFields', 'emergencyContacts', 'emptyEmeregencyFields', 'blankPersonalFields', 'blankJobFields'));
        
    }

    /**
     * Get blank fields from Personal Tab of an Employee
     * @param $empId
     */
    private function getPersonalBlankFields($empId){
        $empFieldsArray = array('employeeNumber,employmentStatus,firstName,middleName,lastName,preferredName,dateOfBirth,gender,maritalStatus,customAllergies,customT-ShirtSize,address1,address2,city,state,zipcode,country,workPhone,workPhoneExtension,mobilePhone,homePhone,workEmail,homeEmail,customCollege,customDegree,customMajor,customGPA,customEducationStartDate,customEducationEndDate');

        $bhr = new BambooAPI("clhmentalhealth");
        $bhr->setSecretKey("40d056dd98d048b1d50c46392c77bd2bbbf0431f");
        //$response = $bhr->getDirectory();
        $getEmployee = $bhr->getEmployee($empId, $empFieldsArray);
        if($getEmployee->isError()) {
        trigger_error("Error communicating with BambooHR: " . $getEmployee->getErrorMessage());
        }

        $getEmployeeData = $getEmployee->getContent();
        $employeePersonalData = json_encode($getEmployeeData);       
        $empPersonalArray = json_decode($employeePersonalData, true);
        $params = substr($empFieldsArray[0], 6, -2);
        //dump($params); dd('---');
        $empKeyArr = explode(',', $params);
        $emptyData = [];
        if(count($empPersonalArray) > 0){     
            if (isset($empPersonalArray['field'])) {
                foreach($empPersonalArray['field'] as $key=> $field){
                    $emptyData[$empKeyArr[$key]]= $this->checkEmptyFields($field);            
                }
            }
        }
        $emptyData = array_filter($emptyData, function($value) {
            return $value !== "";
        });
       // dump($emptyData);
       // dd('checking perosnal tab data');
        return $emptyData;
    }

    private function getJobBlankFields($empId){
        $jobFieldsArray = array('hireDate,originalHireDate,ethnicity,eeo,customEmployeeNumber,customHourlyRate,customPersonalEmail,customHireDate');
        $bhr = new BambooAPI("clhmentalhealth");
        $bhr->setSecretKey("40d056dd98d048b1d50c46392c77bd2bbbf0431f");
        //$response = $bhr->getDirectory();
        $getJobTabData = $bhr->getEmployee($empId, $jobFieldsArray);
        if($getJobTabData->isError()) {
            trigger_error("Error communicating with BambooHR: " . $getJobTabData->getErrorMessage());
        }

        $getEmployeeJobData = $getJobTabData->getContent();
        $employeeJobTabData = json_encode($getEmployeeJobData);       
        $empJobTabArray = json_decode($employeeJobTabData, true);
        $params = 'hireDate,originalHireDate,ethnicity,eeo,customEmployeeNumber,customHourlyRate,customPersonalEmail,customHireDate';
        $empKeyArr = explode(',', $params);
        $emptyData = [];
        if(count($empJobTabArray) > 0){     
            if (isset($empJobTabArray['field'])) {
                foreach($empJobTabArray['field'] as $key=> $field){
                    $emptyData[$empKeyArr[$key]]= $this->checkEmptyFields($field);            
                }
            }
        }
        $emptyData = array_filter($emptyData, function($value) {
            return $value !== "";
        });
        //dump($emptyData);
        ///dd('checking JOB tab data');
        return $emptyData;

        
    }

    public function employeEmptyFieldsCount($empId){
        $blankPersonalFields = $this->getPersonalBlankFields($empId);
        $blankJobFields = $this->getJobBlankFields($empId);
        $html = '<ul style="color:red;">';
        $html .= '<li>Personal : '.count($blankPersonalFields).'</li>';
        $html .= '<li>Job : '.count($blankJobFields).'</li>';
        $html .= '</ul>';
        return $html;
        //dump(count($blankPersonalFields)); dd('--');
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

    private function checkEmptyFields($arrayObj){
        $emptyVal = '';
        if(is_array($arrayObj)){
            $emptyVal = $arrayObj['@attributes']['id'];
        }
        return $emptyVal;
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
