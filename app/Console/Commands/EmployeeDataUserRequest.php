<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployeeController;
use App\Models\EmployeesData;
use App\Models\TimeTrackerData;
use App\Models\EmptyFieldsData;
use App\Models\ReportStatus;
use App\Models\DocumentData;
class EmployeeDataUserRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-employee-data-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    protected $documentController;
    protected $employeeController;

    public function __construct(DocumentController $documentController, EmployeeController $employeeController)
    {
        parent::__construct();
        $this->documentController = $documentController;
        $this->employeeController = $employeeController;
    }
  
    /**
     * Execute the console command.
     */
    public function handle()
    {

        $latestRow = ReportStatus::latest()->first();
       
        if($latestRow->status === 'requested' ){
        $latestRow->update([
            'status' => 'inprocess',
        ]);
        $employeeFieldsIndexes = array(
            'ID'=>17,
            'firstname' => 1,
            'lastname' => 2,
            'email' => 7,
            'department'=>8,
            'jobTitle'=>4,
            'division'=>10,            
        );

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
        $employees = $dataArray['employees'];
        $employees = $employees['employee'];
        $empMainArr = array();
        $i = 0;
  
        foreach($employees as $empKeys=> $emp){
            EmployeesData::truncate();
            EmptyFieldsData::truncate();
            TimeTrackerData::truncate();
            DocumentData::truncate();
            $empID = $emp['@attributes']['id'];
            $empMainArr[$i]['emp_id'] = $empID;
            $empMainArr[$i]['first_name'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['firstname']]);
            $empMainArr[$i]['last_name'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['lastname']]);
            $empMainArr[$i]['email'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['email']]);
            $empMainArr[$i]['department'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['department']]);
            $empMainArr[$i]['job_title'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['jobTitle']]);
            $empMainArr[$i]['division'] = $this->checkIfArray($emp['field'][$employeeFieldsIndexes['division']]);
            $empIdsAr[] = $empID;

            $empty_job_field = 0;
            $empty_personal_field = 0;
            $empty_emergency_field = 0;
            try {
                $blankPersonalFields = $this->employeeController->getPersonalBlankFields($empID);
                $blankJobFields = $this->employeeController->getJobBlankFields($empID);
                $getEmergencyContacts = $this->employeeController->getEmergencyFields($empID);
                $blankEmergencyFields =$getEmergencyContacts['empty'];
                if( count($blankJobFields) > 0 ){
                    $empty_job_field = count($blankJobFields);
                }else if ( count($blankJobFields) == 0) {
                    $empty_job_field = count($blankJobFields);
                }
              
                if( count($blankPersonalFields) > 0 ){
                    $empty_personal_field = count($blankPersonalFields);
                }else if ( count($blankPersonalFields) == 0) {
                    $empty_personal_field = count($blankPersonalFields);
                }
        
                if( count($blankEmergencyFields) > 0 ){
                    $empty_emergency_field = count($blankEmergencyFields);
                }else if ( count($blankEmergencyFields) == 0) {
                    $empty_emergency_field = count($blankEmergencyFields);
                }
            } catch (\Exception $e) {
            }

            $empMainArr[$i]['empty_job_field'] = $empty_job_field;
            $empMainArr[$i]['empty_personal_field'] = $empty_personal_field;
            $empMainArr[$i]['empty_emergency_field'] = $empty_emergency_field;
         $i++;
        }  
        
          EmployeesData::insert($empMainArr);
            $main_employeeTableData = EmployeesData::select()->get();
               foreach ($main_employeeTableData as $result) {
                     $result->emp_id;
                     
                        try {
                        $filteredData = [];
                          $allTypeDate =  $this->employeeController->insertEmployeeExpirationData($result->emp_id , $result->department, $result->job_title, $result->division);
                         foreach ($allTypeDate as &$eachDate) {
                             if ($eachDate['expiration'] != '0000-00-00') {
                             $eachDate['emp_id'] = $result->emp_id;
                             $eachDate['emp_table_id'] = $result->id;
                             unset($eachDate['issuance']);
                             unset($eachDate['status']);
                             unset($eachDate['expirationJCAHO']);
                             $filteredData[] = $eachDate;
                             }
                        }
                         TimeTrackerData::insert($filteredData);
                } catch (\Exception $e) {
                    
                }
                
                    try {
                    
                    $allDocuments = $this->documentController->getDoucumentDataInsertToDb( $result->emp_id, $result->department, $result->job_title, $result->division);
                    $filteredDocData = [];
                         foreach ($allDocuments as &$eachDoc) {
                            if (!isset($eachDoc['files'])) {
                                 $eachDoc['emp_id'] = $result->emp_id;
                                 $eachDoc['emp_table_id'] = $result->id;
                                 $eachDoc['doc_id'] = $eachDoc['docId'];
                                 $eachDoc['doc_name'] = $eachDoc['docName'];
                                 unset($eachDoc['docName']);
                                 unset($eachDoc['docId']);
                                 unset($eachDoc['files']);
                                 $filteredDocData[] = $eachDoc;
                            }
                        }
              
                     DocumentData::insert($filteredDocData);
                             
                } catch (\Exception $e) {
                }
                
                  
                    try {
                     
                    $allFieldsData = $this->employeeController->getEmptyFieldDataInsertToDb( $result->emp_id);
                   
                    $filteredFieldData = [];
                              if(count($allFieldsData['job']) > 0){
                                  foreach ($allFieldsData['job'] as &$job) {
                                      $jobData = [];
                                  $jobData['field_name'] = $job;
                                    $jobData['emp_id'] = $result->emp_id;
                                     $jobData['emp_table_id'] = $result->id;
                                     $jobData['tab'] = 'job';
                                     
                                        $filteredFieldData[] = $jobData;
                                  }
                              }
                                  
                                  if(count($allFieldsData['personal']) > 0){
                                  foreach ($allFieldsData['personal'] as &$personal) {
                                      $eachField = [];
                                    $eachField['emp_id'] = $result->emp_id;
                                     $eachField['emp_table_id'] = $result->id;
                                     $eachField['tab'] = 'personal';
                                     $eachField['field_name'] = $personal;
                                      $filteredFieldData[] = $eachField;
                                  }
                                  }
                                  
                                  if(count($allFieldsData['emergency']) > 0){
                                  foreach ($allFieldsData['emergency'] as &$emergency) {
                                      $emergencyData  = [];
                                    $emergencyData['emp_id'] = $result->emp_id;
                                     $emergencyData['emp_table_id'] = $result->id;
                                     $emergencyData['tab'] = 'emergency';
                                     $emergencyData['field_name'] = $emergency;
                                     $filteredFieldData[] = $emergencyData;
                                  }
                                  }
              
                     EmptyFieldsData::insert($filteredFieldData);
                             
                } catch (\Exception $e) {
                }
                }
                $latestRow->update([
                    'status' => 'created',
                ]);
            }
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
}
