<?php

namespace App\Http\Controllers;

use App\Services\BambooHrService;
use \BambooHR\API\BambooAPI;
use Carbon\Carbon;


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
        $expDateTracker = [];
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
        $getEmergencyContacts = $this->getEmergencyFields($empId);
       //dump($getEmergencyContacts);dd();
        $emergencyContacts = $getEmergencyContacts['filled'];
        $emptyEmeregencyFields = $getEmergencyContacts['empty'];

        $params = 'firstName,lastName,jobTitle,workPhone,mobilePhone,workEmail,department,location,division,supervisor,employmentStatus';
        
        $employeeData = json_encode($getEmployeeData);       
        $dataArray = json_decode($employeeData, true);
        $empKeyArr = explode(',', $params);
      
        $empData = [];
        foreach($dataArray['field'] as $key=> $field){
            $empData[$empKeyArr[$key]]= $this->checkIfArray($field);            
        }
        $empData['ID'] = $empId;
        //dump($empData);  dd('----');

        $empDivision = $empData['division'];
        $empDepartment = $empData['department'];
        $empJobInfo = $empData['jobTitle'];

       //we will get the empty fields from personal tab of an employee
       $blankPersonalFields = $this->getPersonalBlankFields($empId);

       //get the empty fields array from JOB tab of an employee
       $blankJobFields = $this->getJobBlankFields($empId);
       //FIRST CHECK DEPARTMENT
       if($empDepartment == env('GROUP_HOME')){ //if department is `Residential Group Home` 
            if($empJobInfo == env('JOBINFO_GROUP_HOME_CHILD_YOUTH')){ //Group Home Residential Child Youth Care Practitioner
                $expDateTracker[] = $this->getDateTrackers($empId, 'License');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Insurance');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Record');
                $expDateTracker[] = $this->getDateTrackers($empId, 'First_Aid');
                $expDateTracker[] = $this->getDateTrackers($empId, 'TB_Test');
                $expDateTracker[] = $this->getDateTrackers($empId, 'RCYCP_Certification');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Tact_II');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_Evaluation');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_EvaluationJC');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Sexual_Abuse_Awareness');
            }else if($empJobInfo == env('JOBINFO_GROUP_HOME_YOUTH')){ //Group Home Youth	
                $expDateTracker[] = $this->getDateTrackers($empId, '72_Hour_Treatment_Plan');
                $expDateTracker[] = $this->getDateTrackers($empId, '30_Day_Treatment_Plan');
                $expDateTracker[] = $this->getDateTrackers($empId, '90_Day_Treatment_Plan');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Psych_Evaluation');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Safe_Environment_Plan');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Physical');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Dental');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Vision');
            }
        }else if ($empDepartment == env('DEPARTMENT_PRP')){ //when the department is PRP and then division is `Family Care Coordinator`|| `Family Care Coordinator`
            if($empDivision == env('DIVISION_PRP_FAMILY_COORD')){
                $expDateTracker[] = $this->getDateTrackers($empId, 'License');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Insurance');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Record');
                $expDateTracker[] = $this->getDateTrackers($empId, 'First_Aid');
                $expDateTracker[] = $this->getDateTrackers($empId, 'TB_Test');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_Liability');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_EvaluationJC');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_Evaluation');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Sexual_Abuse_Awareness');
            }else if($empDivision == env('DIVISION_PRP_COORDINATOR_SPEC')){ //specialist
                $expDateTracker[] = $this->getDateTrackers($empId, 'License');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Insurance');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Record');
                $expDateTracker[] = $this->getDateTrackers($empId, 'First_Aid');
                $expDateTracker[] = $this->getDateTrackers($empId, 'TB_Test');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_Liability');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_EvaluationJC');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_Evaluation');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Sexual_Abuse_Awareness');
            }
        }else if($empDepartment == env('DEPARTMENT_OMHC')){
            if($empJobInfo == env('JOBINFO_COOCCURING_OMHC')){
                $expDateTracker[] = $this->getDateTrackers($empId, 'License');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Insurance');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Record');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_License');
                $expDateTracker[] = $this->getDateTrackers($empId, 'First_Aid');
                $expDateTracker[] = $this->getDateTrackers($empId, 'TB_Test');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_Liability');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_EvaluationJC');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_Evaluation');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Sexual_Abuse_Awareness');
                $expDateTracker[] = $this->getDateTrackers($empId, 'National_Practitioner_Data_Bank');
            }if($empJobInfo == env('JOBINFO_Intern_OMHC')){ //when the department is `OMHC;Substance Use Disorder (SUD)` and jobtitle is `INTERN`
                $expDateTracker[] = $this->getDateTrackers($empId, 'License');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Insurance');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Record');
                $expDateTracker[] = $this->getDateTrackers($empId, 'First_Aid');
                $expDateTracker[] = $this->getDateTrackers($empId, 'TB_Test');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_Liability');

            }
        }else if($empDepartment == env('DEPARTMENT_MENTAL_HEALTH_OMHC')){ //when department is OMHC and MENTAL HEALTH
            if($empJobInfo == env('JOBINFO_MENTAL_HEALTH_OMHC')){
                $expDateTracker[] = $this->getDateTrackers($empId, 'License');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Insurance');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Record');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_License');
                $expDateTracker[] = $this->getDateTrackers($empId, 'First_Aid');
                $expDateTracker[] = $this->getDateTrackers($empId, 'TB_Test');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_Liability');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_EvaluationJC');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_Evaluation');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Sexual_Abuse_Awareness');
                $expDateTracker[] = $this->getDateTrackers($empId, 'National_Practitioner_Data_Bank');
            }else if($empJobInfo == env('JOBINFO_Clinical_OMHC')){ //when department is OMHC and jobtitle is `clinical director`
                $expDateTracker[] = $this->getDateTrackers($empId, 'License');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Insurance');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Record');
                $expDateTracker[] = $this->getDateTrackers($empId, 'First_Aid');
                $expDateTracker[] = $this->getDateTrackers($empId, 'TB_Test');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_License');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_Liability');
                $expDateTracker[] = $this->getDateTrackers($empId, 'National_Practitioner_Data_Bank');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_EvaluationJC');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_Evaluation');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Sexual_Abuse_Awareness');
            }else if($empJobInfo == env('JOBINFO_Nurse_Practitioner_OMHC')){  // jobtitle is `Psychiatric Nurse Practitioner`
                $expDateTracker[] = $this->getDateTrackers($empId, 'License');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Insurance');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Record');
                $expDateTracker[] = $this->getDateTrackers($empId, 'First_Aid');
                $expDateTracker[] = $this->getDateTrackers($empId, 'TB_Test');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_License');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_Liability');
                $expDateTracker[] = $this->getDateTrackers($empId, 'National_Practitioner_Data_Bank');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_EvaluationJC');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_Evaluation');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Sexual_Abuse_Awareness');
                $expDateTracker[] = $this->getDateTrackers($empId, 'Psychiatric_Nurse_Practitioner_Certification');
                $expDateTracker[] = $this->getDateTrackers($empId, 'CDS_Registration');
                $expDateTracker[] = $this->getDateTrackers($empId, 'DEA_Registration');
            }
        }

       //get expiration tracker Dates
       /*$expDateTracker[] = $this->getDateTrackers($empId, 'License');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Insurance');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Record');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_License');
       $expDateTracker[] = $this->getDateTrackers($empId, 'First_Aid');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Tact_II');
       $expDateTracker[] = $this->getDateTrackers($empId, 'TB_Test');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Professional_Liability');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Other_Professional_License');
       $expDateTracker[] = $this->getDateTrackers($empId, 'RCYCP_Certification');
       $expDateTracker[] = $this->getDateTrackers($empId, 'DEA_Registration');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Psychiatric_Nurse_Practitioner_Certification');
       $expDateTracker[] = $this->getDateTrackers($empId, 'CDS_Registration');
       $expDateTracker[] = $this->getDateTrackers($empId, 'National_Practitioner_Data_Bank');
        $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_Evaluation');
        $expDateTracker[] = $this->getDateTrackers($empId, 'Annual_EvaluationJC');
       $expDateTracker[] = $this->getDateTrackers($empId, '72_Hour_Treatment_Plan');
       $expDateTracker[] = $this->getDateTrackers($empId, '30_Day_Treatment_Plan');
       $expDateTracker[] = $this->getDateTrackers($empId, '90_Day_Treatment_Plan');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Psych_Evaluation');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Safe_Environment_Plan');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Physical');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Dental');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Vision');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Sexual_Abuse_Awareness');
       $expDateTracker[] = $this->getDateTrackers($empId, 'Medication_Technician_Certificate');*/
       // dump($expDateTracker); dd('-------------');

        return view('dashboard.employee',compact('empData', 'base64Image', 'jobFields', 'emergencyContacts', 'emptyEmeregencyFields', 'blankPersonalFields', 'blankJobFields','expDateTracker'));
        
    }

    /**
     * Get blank fields from Personal Tab of an Employee
     * @param $empId
     */
    private function getPersonalBlankFields($empId){
        $empFieldsArray = array('employeeNumber,employmentStatus,firstName,lastName,dateOfBirth,gender,maritalStatus,customAllergies,customT-ShirtSize,address1,city,state,zipcode,country,mobilePhone,workEmail,homeEmail,customCollege,customDegree,customGPA,customEducationStartDate,customEducationEndDate');

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

    private function getEmergencyFields($empId){
        //GET EMERGENCY TAB 
        $emergencyContacts = $emptyEmeregencyFields = $finalArr = [];
        $bhr = new BambooAPI("clhmentalhealth");
        $bhr->setSecretKey("40d056dd98d048b1d50c46392c77bd2bbbf0431f");
        $getEmergencyContacts = $bhr->getTable($empId, 'emergencyContacts');
        
        if($getEmergencyContacts->isError()) {
            trigger_error("Error communicating with BambooHR: " . $getEmergencyContacts->getErrorMessage());
        }
        $getEmergencyContacts = $getEmergencyContacts->getContent();
        
        $getEmergencyContacts = json_encode($getEmergencyContacts);       
        $getEmergencyContacts = json_decode($getEmergencyContacts, true);
       
        if(count($getEmergencyContacts) > 0){
            $emergency = $getEmergencyContacts['row'];        
            if (isset($emergency['field'])) 
            {                
                $requiredFields = $this->getEmeregencyFields($emergency['field']);
                foreach($requiredFields as $key=> $field)
                {
                    //dump($field);
                    $emergencyContacts[] = $this->checkIfArray($field);
                    $emptyEmeregencyFields[] = $this->checkEmptyFields($field);
                }
            }
        }
        
        //return the empty fileds 
        $emptyEmeregencyFields = array_filter($emptyEmeregencyFields, function($value) {
            return $value !== "";
        });
    
        $finalArr = ['filled'=>$emergencyContacts, 'empty'=>$emptyEmeregencyFields];
        return $finalArr;
    }

    /**Will return only specific fields from EMERGENCY CONTACT TAB
     * according to document shared
     */
    private function getEmeregencyFields($fieldsArr){
       
        $keysToRemove = ['2','4','5', '12']; // removed EXT, HomePhone, MobilePhone, Street2 fields
        foreach ($keysToRemove as $key) {
            if (array_key_exists($key, $fieldsArr)) {
                unset($fieldsArr[$key]);
            }
        }
        return $fieldsArr;
    }

    /**THIS METHOD RUNS to get the dates that are
     * GOING TO EXPIRE
     * ALREADY EXPIRED
     * According to the role/department
     */
    private function getDateTrackers($empId, $trackerType){
        $expFieldsArray = [];
        $params = '';

        switch($trackerType){
            case 'License':
                $expFieldsArray = array("customDriver'sLicenseIssuanceDate,customDriver'sLicenseExpirationDate");
                $params = "driver_issuance,driver_expiration";
            break;
            case "Insurance":
                $expFieldsArray = array("customDriver'sInsuranceIssuanceDate,customDriver'sInsuranceExpirationDate");
                $params = "driver_insurance_issuance,driver_insurance_expiration";
            break;
            case "Record":
                $expFieldsArray = array("customDrivingRecordIssuanceDate,customDrivingRecordExpirationDate");
                $params = "record_issuance,record_expiration";
            break;
            case "Professional_License":
                $expFieldsArray = array("customProfessionalLicenseIssuanceDate1,customProfessionalLicenseIssuanceDate");
                $params = "professional_issuance,professional_expiration";
            break;
            case "First_Aid":
                $expFieldsArray = array("customFirstAid/CPRIssuanceDate,customFirstAid/CPRExpirationDate");
                $params = "firstaid_issuance,firstaid_expiration";
            break;
            case "Tact_II":
                $expFieldsArray = array("customTactIIIssuanceDate,customTactIIExpirationDate");
                $params = "tact_issuance,tact_expiration";
            break;
            case "TB_Test":
                $expFieldsArray = array("customTBTestResultsDate,customTBTestResultsExpirationDate");
                $params = "tbtest_issuance,tbtest_expiration";
            break;
            case "Professional_Liability":
                $expFieldsArray = array("customProfessionalLiabilityInsuranceIssuanceDate,customProfessionalLiabilityInsuranceExpirationDate");
                $params = "liability_issuance,liability_expiration";
            break;
            case "Other_Professional_License":
                $expFieldsArray = array("customOtherProfessionalLicenseIssuanceDate,customOtherProfessionalLicenseExpirationDate");
                $params = "other_professional_issuance,liability_professional_expiration";
            break;
            case "RCYCP_Certification":
                $expFieldsArray = array("customRCYCPCertificationIssuanceDate,customRCYCPCertificationExpirationDate");
                $params = "RCYCP_certification_issuance,RCYCP_certification_expiration";
            break;
            case "DEA_Registration":
                $expFieldsArray = array("customDEALicenseIssuanceDate,customDEALicenseExpirationDate");
                $params = "DEA_registration_issuance,DEA_registration_expiration";
            break;
            case "Psychiatric_Nurse_Practitioner_Certification":
                $expFieldsArray = array("customPsychiatricNursePractitionerCertificationIssuanceDate,customPsychiatricNursePractitionerCertificationExpirationDate");
                $params = "Psychiatric_Nurse_Practitioner_issuance,Psychiatric_Nurse_Practitioner_expiration";
            break;
            case "CDS_Registration":
                $expFieldsArray = array("customCDSRegistrationIssuanceDate,customCDSRegistrationExpirationDate");
                $params = "CDS_Registration_issuance,CDS_Registration_expiration";
            break;
            case "National_Practitioner_Data_Bank":
                $expFieldsArray = array("customNPDBQueryDate,customNPDBQueryExpirationDate");
                $params = "National_Practitioner_Data_issuance,National_Practitioner_Data_expiration";
            break;
            case "Annual_Evaluation":
                $expFieldsArray = array("customAnnualEvaluationExpirationDate,customJCAHO/AnnualTrainingsExpirationDate");
                $params = "Annual_Evaluation_expiration,JCAHO_Annual_Trainings_expirationJCAHO";
            break;
            case "Annual_EvaluationJC":
                $expFieldsArray = array("customAnnualEvaluationExpirationDate,customJCAHO/AnnualTrainingsExpirationDate");
                $params = "Annual_Evaluation_expiration,JCAHO_Annual_Trainings_expirationJCAHO";
            break;
            case "72_Hour_Treatment_Plan":
                $expFieldsArray = array("custom72HourTreatmentPlanIssuanceDate,custom72HourTreatmentPlanExpirationDate");
                $params = "72_Hour_issuance,72_Hour_expiration";
            break;
            case "30_Day_Treatment_Plan":
                $expFieldsArray = array("custom30DayTreatmentPlanIssuanceDate,custom30DayTreatmentPlanExpirationDate");
                $params = "30_Day_Treatment_issuance,30_Day_Treatment_expiration";
            break;
            case "90_Day_Treatment_Plan":
                $expFieldsArray = array("custom90DayTreatmentPlanIssuanceDate,custom90DayTreatmentPlanExpirationDate");
                $params = "90_Day_Treatment_issuance,90_Day_Treatment_expiration";
            break;
            case "Psych_Evaluation":
                $expFieldsArray = array("customPsychEvaluationIssuanceDate,customPsychEvaluationExpirationDate");
                $params = "Psych_Evaluation_issuance,Psych_Evaluation_expiration";
            break;
            case "Safe_Environment_Plan":
                $expFieldsArray = array("customSafeEnvironmentPlanIssuanceDate,customSafeEnvironmentPlanExpirationDate");
                $params = "Safe_Environment_issuance,Safe_Environment_expiration";
            break;
            case "Physical":
                $expFieldsArray = array("customPhysicalIssuanceDate,customPhysicalExpirationDate");
                $params = "Physical_issuance,Physical_expiration";
            break;
            case "Dental":
                $expFieldsArray = array("customDentalIssuanceDate,customDentalExpirationDate");
                $params = "Dental_issuance,Dental_expiration";
            break;
            case "Vision":
                $expFieldsArray = array("customVisionIssuanceDate,customVisionExpirationDate");
                $params = "Vision_issuance,Vision_expiration";
            break;
            case "Sexual_Abuse_Awareness":
                $expFieldsArray = array("customSexualAbuseAwareness&PreventionIssuanceDate,customSexualAbuseAwareness&PreventionExpirationDate");
                $params = "Sexual_Abuse_Awareness_issuance,Sexual_Abuse_Awareness_expiration";
            break;
            case "Medication_Technician_Certificate":
                $expFieldsArray = array("customMedicationDateTracker,customMedicationTechnicianCertificateExpirationDate");
                $params = "Medication_Technician_issuance,Medication_Technician_expiration";
            break;
        }        

        
        $bhr = new BambooAPI("clhmentalhealth");
        $bhr->setSecretKey("40d056dd98d048b1d50c46392c77bd2bbbf0431f");
        //$response = $bhr->getDirectory();
        $getExpTabData = $bhr->getEmployee($empId, $expFieldsArray);
        if($getExpTabData->isError()) {
            trigger_error("Error communicating with BambooHR: " . $getExpTabData->getErrorMessage());
        }

        $getEmployeeExpData = $getExpTabData->getContent();
        $employeeJobTabData = json_encode($getEmployeeExpData);       
        $empExpTabArray = json_decode($employeeJobTabData, true);
        
        $empKeyArr = explode(',', $params);
        $trackerData = [];
        $annualJC_array = [];
        // Get today's date
        $today = Carbon::now()->startOfDay();
        if(count($empExpTabArray) > 0){     
            if (isset($empExpTabArray['field'])) {
                $trackerData['type'] = $trackerType;                
                foreach($empExpTabArray['field'] as $key=> $field){    
                    $keyName =  explode('_', $empKeyArr[$key]);
                    $keyName = end($keyName);
                    $trackerData[$keyName]= $field;
                    $trackerData['status'] = 'ACTIVE';
                    if ($field == '0000-00-00') {
                        $trackerData['status'] = 'NODATE';
                    } else if($keyName == 'expiration' || $keyName == 'expirationJCAHO') {
                        // Convert the given date string into a Carbon instance
                        $date = Carbon::createFromFormat('Y-m-d', $field)->startOfDay();

                        // Calculate the difference between today's date and the given date
                        $differenceInDays = $today->diffInDays($date, false);

                        if ($differenceInDays < 0) {
                            // Date is already expired
                            $trackerData['status'] = 'EXPIRED';
                        } elseif ($differenceInDays >= 15 && $differenceInDays <= 30) {
                            // Date is going to expire within the next 15 to 30 days
                            $trackerData['status'] = 'GETTINGEXPIRE';
                        }
                    }
                }
                if($trackerData['type'] == 'Annual_Evaluation' ){ 
                    $trackerData['issuance'] = "0000-00-00";                    
                    
                }else if($trackerData['type'] == 'Annual_EvaluationJC' ){                    
                        $trackerData['type'] = 'JCAHO';
                        $trackerData['issuance'] = '0000-00-00';
                        $trackerData['expiration'] = $trackerData['expirationJCAHO'];
                    
                }
            }
        }
        return $trackerData;
    }


    public function employeEmptyFieldsCount($empId){
        $colorBClass = $colorPClass = '';
        $blankPersonalFields = $this->getPersonalBlankFields($empId);
        $blankJobFields = $this->getJobBlankFields($empId);
        $getEmergencyContacts = $this->getEmergencyFields($empId);
        $blankEmergencyFields =$getEmergencyContacts['empty'];
       // dump($blankEmergencyFields); dd('---------');
        if( count($blankJobFields) > 0 ){
            $colorBClass = 'color:red;';
        }else if ( count($blankJobFields) == 0) {
            $colorBClass = 'color:green;';
        }
                
        if( count($blankPersonalFields) > 0 ){
            $colorPClass = 'color:red;';
        }else if ( count($blankPersonalFields) == 0) {
            $colorPClass = 'color:green;';
        }

        if( count($blankEmergencyFields) > 0 ){
            $colorEClass = 'color:red;';
        }else if ( count($blankEmergencyFields) == 0) {
            $colorEClass = 'color:green;';
        }

        $html = '<ul>';
        $html .= '<li style="'.$colorPClass.'">Personal : '.count($blankPersonalFields).'</li>';
        $html .= '<li style="'.$colorBClass.'">Job : '.count($blankJobFields).'</li>';
        $html .= '<li style="'.$colorEClass.'">Emergency : '.count($blankEmergencyFields).'</li>';
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
